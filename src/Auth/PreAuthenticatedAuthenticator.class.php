<?php

namespace Auth;

use Exception;

class PreAuthenticatedAuthenticator extends BaseAuthenticator
{
	/**
	 * Optional refresh token.
	 *
	 * @var string|null
	 */
	private ?string $refreshToken;

	/**
	 * Required additional information for the refresh process.
	 *
	 * @var string|null
	 */
	private ?string $tenantId;

	/**
	 * Required additional information for the refresh process.
	 *
	 * @var string|null
	 */
	private ?string $clientId;

	/**
	 * Constructor.
	 *
	 * @param string      $accessToken  The current access token.
	 * @param int         $tokenExpires Unix timestamp when the token expires.
	 * @param string|null $refreshToken Optional refresh token.
	 * @param string|null $tenantId     Required if $refreshToken is set.
	 * @param string|null $clientId     Required if $refreshToken is set.
	 */
	public function __construct(
		string  $accessToken,
		int     $tokenExpires,
		?string $refreshToken = null,
		?string $tenantId = null,
		?string $clientId = null
	)
	{
		$this->accessToken = $accessToken;
		$this->tokenExpires = $tokenExpires;
		$this->refreshToken = $refreshToken;
		$this->tenantId = $tenantId;
		$this->clientId = $clientId;
	}

	/**
	 * Returns the current access token.
	 * If the token expires, it attempts to automatically
	 * renew it using the refresh token.
	 *
	 * @return string
	 * @throws Exception 
	 */
	public function getAccessToken(): string
	{
		if ($this->accessToken === null || time() >= $this->tokenExpires - 60) {
			if ($this->refreshToken !== null) {
				$this->refreshAccessToken();
			} else {
				throw new Exception("Pre-authenticated token has expired or is not available.");
			}
		}
		return $this->accessToken;
	}

	/**
	 * Renews the access token using the refresh token.
	 *
	 * @throws Exception If not all required information is available
	 *                   or the refresh process fails.
	 */
	private function refreshAccessToken(): void
	{
		if (!$this->tenantId || !$this->clientId || !$this->refreshToken) {
			throw new Exception("Insufficient information to update the token.");
		}

		$url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";
		$postFields = http_build_query([
			'grant_type' => 'refresh_token',
			'client_id' => $this->clientId,
			'refresh_token' => $this->refreshToken,
			'scope' => 'https://graph.microsoft.com/.default',
		]);

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postFields,
			CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if ($response === false || $httpCode !== 200) {
			$errorMsg = $response;
			$json = json_decode($response, true);
			if (isset($json['error_description'])) {
				$errorMsg = $json['error_description'];
			} else if (isset($json['error']['message'])) {
				$errorMsg = $json['error']['code'] . ": " . $json['error']['message'];
			}
			throw new Exception("Token refresh failed ($httpCode): $errorMsg\ncURL: $curlError");
		}

		$data = json_decode($response, true);
		if (!isset($data['access_token'])) {
			throw new Exception("Token refresh response invalid: Access token missing.");
		}

		$this->accessToken = $data['access_token'];
		$this->tokenExpires = time() + ($data['expires_in'] ?? 3600);

		// If a new refresh token is returned by the provider, use it.
		if (isset($data['refresh_token'])) {
			$this->refreshToken = $data['refresh_token'];
		}
	}
}
