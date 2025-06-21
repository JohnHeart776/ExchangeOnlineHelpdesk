<?php

require_once __DIR__ . '/../src/bootstrap.php';
global $d;

use Auth\GraphCertificateAuthenticator;
use Client\GraphClient;

if (isset($_GET['code'])) {
	$code = $_GET['code'];

	// Token-Anforderung
	$postData = [
		'client_id' => Config::getConfigValueFor('user.clientId'),
		'client_secret' => Config::getConfigValueFor('user.clientSecret'),
		'code' => $code,
		'redirect_uri' => Config::getConfigValueFor('user.redirectUri'),
		'grant_type' => 'authorization_code',
		'scope' => Config::getConfigValueFor('user.oauthScopes'),
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, Config::getConfigValueFor('user.tokenUrl'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
	$response = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($response, true);

	if (isset($data['access_token'])) {

		$accessToken = $data['access_token'];
		$refreshToken = $data['refresh_token'] ?? null;

		$_SESSION['tenant_id'] = Config::getConfigValueFor('tenantId');

		// Benutzer-Infos via Microsoft Graph
		$ch = curl_init("https://graph.microsoft.com/v1.0/me");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $accessToken",
		]);
		$userResponse = curl_exec($ch);
		curl_close($ch);

		$userData = json_decode($userResponse, true);

		// Organisationsinfos via GraphApplication abrufen
		$auth = new GraphCertificateAuthenticator(
			tenantId: Config::getConfigValueFor('tenantId'),
			clientId: Config::getConfigValueFor('application.clientId'),
			cert: Config::getConfigValueFor('application.certificate'),
			key: Config::getConfigValueFor('application.certificateKey'),
			keyPass: Config::getConfigValueFor('application.certificateKeyPassword')
		);
		$graphClient = new GraphClient($auth);
		$orgInfo = $auth->getOrganizationInfo();

		if (isset($userData['id'])) {

			$user = Login::loginUserFromGraphResponse($userData, $orgInfo, $accessToken, $refreshToken);
			if (!$user->isEnabled()) {
				login::logout();
				die("Your account is disabled. Please contact the administrator.");
			}

			header('Location: /');
			exit;
		} else {
			echo "Error retrieving user information.";
		}
	} else {
		echo "Error: Token could not be received. Go to <a href='/'>Index</a>";
	}
} else {
	echo "Error: No authorization code received. Go to <a href='/'>Index</a>";
}
