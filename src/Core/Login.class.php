<?php

use Client\GraphClient;
use Struct\GraphUserLoginResponse;

class Login
{
	public static function isLoggedIn(): bool
	{
		return is_a($_SESSION["user"], User::class) && !empty($_SESSION['UserId']);
	}

	public static function getUser(): ?User
	{
		if (!self::isLoggedIn()) {
			return null;
		}

		return $_SESSION["user"];

	}

	public static function getAccessToken(): ?string
	{
		self::refreshAccessTokenIfNeeded();
		return $_SESSION["access_token"] ?? null;
	}

	public static function refreshAccessTokenIfNeeded(): void
	{
		if (!self::isLoggedIn()) {
			return;
		}

		$user = self::getUser();
		if (!$user || empty($_SESSION['refresh_token']) || empty($_SESSION['access_token'])) {
			self::logout();
			return;
		}

		$accessToken = $_SESSION['access_token'];
		$parts = explode('.', $accessToken);

		if (count($parts) !== 3) {
			self::logout();
			return;
		}

		$payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
		$exp = $payload['exp'] ?? 0;

		if (!$exp) {
			self::logout();
			return;
		}

		// Update session timer unabhängig vom Refresh
		$_SESSION['expires_in'] = $exp - time();
		$expiresAt = date('Y-m-d H:i:s', $exp);

		// Noch gültig?
		if (time() < $exp - self::getTokenRefreshThreshold()) {
			return;
		}

		// Token erneuern
		try {
			$tokenUrl = Config::getConfigValueFor('user.tokenUrl');

			$postData = [
				'client_id' => Config::getConfigValueFor('user.clientId'),
				'client_secret' => Config::getConfigValueFor('user.clientSecret'),
				'refresh_token' => $_SESSION['refresh_token'],
				'grant_type' => 'refresh_token',
				'scope' => Config::getConfigValueFor('user.oauthScopes'),
			];

			$ch = curl_init($tokenUrl);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($postData),
			]);

			$response = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($response, true);

			if (!isset($data['access_token'])) {
				self::logout();
				// Optional: Logging z.B. error_log('Token Refresh fehlgeschlagen: ' . json_encode($data));
				return;
			}

			$newAccessToken = $data['access_token'];
			$newRefreshToken = $data['refresh_token'] ?? $_SESSION['refresh_token'];

			// Neues Expiry aus JWT berechnen
			$newParts = explode('.', $newAccessToken);
			$newPayload = json_decode(base64_decode(strtr($newParts[1], '-_', '+/')), true);
			$newExp = $newPayload['exp'] ?? (time() + 3600);

			//Update die Session
			$_SESSION['access_token'] = $newAccessToken;
			$_SESSION['refresh_token'] = $newRefreshToken;
			$_SESSION['expires_in'] = $newExp - time();

		} catch (\Throwable $e) {
			// Optional: error_log('Token Refresh Exception: ' . $e->getMessage());
			self::logout();
		}
	}

	private static function getTokenRefreshThreshold(): int
	{
		$threshold = Config::getConfigValueFor("session_refresh_threshold");
		return (int)$threshold;
	}

	public static function initSession(): void
	{
		session_start();
		//check if session is active, if not, then start
		if (!isset($_SESSION["sid"])) {
			$_SESSION["sid"] = session_id();

			$_SESSION['access_token'] = null;
			$_SESSION['refresh_token'] = null;
			$_SESSION['expires_in'] = null;
			$_SESSION['user_upn'] = null;
			$_SESSION['UserId'] = null;
			$_SESSION['AzureObjectId'] = null;
			$_SESSION["user"] = null;
		}

	}

	/**
	 * @param User   $user
	 * @param string $accessToken
	 * @param string $refreshToken
	 * @param int    $expiresIn
	 * @return void
	 */
	public static function startSession(User $user, string $accessToken, string $refreshToken, int $expiresIn = 3600): void
	{
		// Regenerate session ID for security (prevent session fixation)
		session_regenerate_id(true);

		$_SESSION['access_token'] = $accessToken;
		$_SESSION['refresh_token'] = $refreshToken;
		$_SESSION['expires_in'] = $expiresIn;

		$_SESSION['user_upn'] = $user->Upn;
		$_SESSION['UserId'] = $user->UserId;
		$_SESSION['AzureObjectId'] = $user->AzureObjectId;

		$_SESSION["user"] = $user;
	}

	public static function loginUserFromGraphResponse(array  $graphData,
													  array  $orgInfo,
													  string $accessToken,
													  string $refreshToken): User
	{

		global $d;

		$now = date('Y-m-d H:i:s');
		$expiresIn = $_SESSION['expires_in'] ?? 3600;

		$tenantId = $orgInfo["tenantId"];

		$_q_exist = "SELECT UserId, Guid FROM User WHERE AzureObjectId = :oid AND TenantId = :tid LIMIT 1";
		$existingUser = $d->fetchOnePDO($_q_exist, [
			'oid' => $graphData['id'],
			'tid' => $orgInfo['tenantId'],
		]);

		$graphUserLoginResponse = new \Struct\GraphUserLoginResponse($graphData);

		if (empty($existingUser)) {
			//user gibt es noch nicht!
			$newUser = new User(0);
			$newUser->fromGraphLoginResponseData($orgInfo, $graphData);
			$User = UserController::save($newUser);
		} else {
			//den user gibts schon
			$User = new User((int)$existingUser['UserId']);
			$graphUserLoginResponse->compareAndUpdateUser($User);
		}

		//update the last Login
		$User->update("LastLogin", DateHelper::getDateTimeForMysql());

		//start the session
		self::startSession($User, $accessToken, $refreshToken, $expiresIn);

		$client = GraphHelper::getApplicationAuthenticatedGraph();

		$GraphUserImage = $client->getUserImage($User->Upn);

		if ($GraphUserImage?->hasImage()) {

			$UserImage = UserImageController::searchBy("UserId", $User->UserId, true);
			if ($UserImage) {
				$UserImage->update("Base64Image", $GraphUserImage->getBase64());
			} else {
				$newUserImage = new UserImage(0);
				$newUserImage->UserId = $User->UserId;
				$newUserImage->Base64Image = $GraphUserImage->getBase64();
				$UserImage = UserImageController::save($newUserImage);
			}
			$UserImage->update("LastUpdated", DateHelper::getDateTimeForMysql());

		}

		return $User;
	}

	public static function logout(): void
	{
		// Clear all authentication-related session variables
		unset(
			$_SESSION['user'],
			$_SESSION['UserId'],
			$_SESSION['AzureObjectId'],
			$_SESSION['user_guid'],
			$_SESSION['access_token'],
			$_SESSION['refresh_token'],
			$_SESSION['expires_in'],
			$_SESSION['user_upn'],
			$_SESSION['user_name']
		);

		// Regenerate session ID for security
		session_regenerate_id(true);
	}

	/**
	 * @return void
	 */
	public static function requireLogin(): void
	{
		if (!self::isLoggedIn()) {
			self::bringToLogin(useCurrentPageAsRedirectTarget: true);
		}
	}

	public static function isGuest(): bool
	{
		if (!self::isLoggedIn()) {
			return false;
		}
		return self::getUser()->GetUserRole() == "guest";
	}

	public static function isUser(): bool
	{
		if (!self::isLoggedIn()) {
			return false;
		}

		if (self::isAgent()) //if we're agent, we're also user
			return true;

		return self::getUser()->GetUserRole() == "user";
	}

	public static function isAgent(): bool
	{
		if (!self::isLoggedIn()) {
			return false;
		}

		if (self::isAdmin()) //if we're admin, we're also agent
			return true;

		return self::getUser()->isAgent();
	}

	public static function isAdmin(): bool
	{
		if (!self::isLoggedIn()) {
			return false;
		}
		return self::getUser()->isAdmin();
	}

	public static function requireIsGuest()
	{
		if (!self::isGuest()) {
			self::bringToLogin(
				useCurrentPageAsRedirectTarget: true,
				message: "You must be a Guest to access this Page. Please contact your administrator."
			);
		}
	}

	public static function requireIsUser()
	{
		if (!self::isUser()) {
			self::bringToLogin(
				useCurrentPageAsRedirectTarget: true,
				message: "You must be a User to access this Page. Please contact your administrator."
			);
		}
	}

	public static function requireIsAgent()
	{
		if (!self::isAgent()) {
			self::bringToLogin(
				useCurrentPageAsRedirectTarget: true,
				message: "Du musst Agent sein um dies zu tun. Bitte wende dich an deinen Administrator."
			);
		}
	}

	public static function requireIsAdmin()
	{
		if (!self::isAdmin()) {
			self::bringToLogin(
				useCurrentPageAsRedirectTarget: true,
				message: "Du musst ein Administrator sein, um diese Aktion durchzuführen."
			);
		}
	}


	public static function bringToDashboard(): void
	{
		header("Location: /dashboard/");
		exit;
	}

	public static function bringToLogin(
		?string $redirectTarget = null,
		?bool   $useCurrentPageAsRedirectTarget = null,
		?string $message = null): void
	{
		if ($useCurrentPageAsRedirectTarget) {
			$redirectTarget = $_SERVER['REQUEST_URI'] ?? '/';
			// Validate redirect target to prevent header injection
			$redirectTarget = filter_var($redirectTarget, FILTER_SANITIZE_URL);
		}

		$location = '/login/';
		$params = [];

		if ($redirectTarget) {
			$params['from'] = $redirectTarget;
		}

		if ($message) {
			$params['message'] = $message;
		}

		if (!empty($params)) {
			$location .= '?' . http_build_query($params);
		}

		header("Location: {$location}");
		exit;
	}

}
