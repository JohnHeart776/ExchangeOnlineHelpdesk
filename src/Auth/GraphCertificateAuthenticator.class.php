<?php

namespace Auth;

use Exception;
use Logger;

class GraphCertificateAuthenticator extends BaseAuthenticator
{
	private string $tenantId;
	private string $clientId;
	private string $cert;
	private string $key;
	private ?string $keyPass;

	private $privateKey;
	private string $certThumbprint;

	public function __construct(
		string  $tenantId,
		string  $clientId,
		string  $cert,
		string  $key,
		?string $keyPass = null
	)
	{
		$this->tenantId = $tenantId;
		$this->clientId = $clientId;
		$this->cert = $cert;
		$this->key = $key;
		$this->keyPass = $keyPass;

		$this->loadKeys();
	}

	/**
	 * Loads the private key and calculates the certificate thumbprint.
	 *
	 * @throws Exception
	 */
	private function loadKeys(): void
	{
		$this->privateKey = openssl_pkey_get_private($this->key, $this->keyPass);
		if ($this->privateKey === false) {
			Logger::getInstance()->log("Private key is invalid or password is wrong.");
			throw new Exception("Private key is invalid or password is wrong.");
		}

		$certPem = trim($this->cert);
		$certPem = str_replace(["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r", "\n"], "", $certPem);
		$certDer = base64_decode($certPem);
		if ($certDer === false) {
			Logger::getInstance()->log("Certificate could not be decoded.");
			throw new Exception("Certificate could not be decoded (wrong format?).");
		}

		$this->certThumbprint = $this->base64UrlEncode(sha1($certDer, true));
	}

	/**
	 * Requests (or delivers an already valid) access token.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getAccessToken(): string
	{
		if ($this->accessToken !== null && time() < $this->tokenExpires - 60) {
			return $this->accessToken;
		}

		$jwt = $this->createJwtAssertion();

		$postFields = http_build_query([
			'grant_type' => 'client_credentials',
			'client_id' => $this->clientId,
			'scope' => 'https://graph.microsoft.com/.default',
			'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
			'client_assertion' => $jwt,
		]);

		$url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

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
			Logger::getInstance()->log("Token request failed ($httpCode): $errorMsg\ncURL: $curlError");
			throw new Exception("Token request failed ($httpCode): $errorMsg");
		}

		$data = json_decode($response, true);
		if (!isset($data['access_token'])) {
			Logger::getInstance()->log("Token response invalid: 'access_token' missing.");
			throw new Exception("Token response invalid: Access Token not received.");
		}

		$this->accessToken = $data['access_token'];
		$this->tokenExpires = time() + ($data['expires_in'] ?? 3600);

		return $this->accessToken;
	}

	/**
	 * Creates the JWT assertion for client authentication.
	 *
	 * @return string
	 * @throws Exception
	 */
	private function createJwtAssertion(): string
	{
		$now = time();
		$payload = [
			'aud' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
			'iss' => $this->clientId,
			'sub' => $this->clientId,
			'jti' => bin2hex(random_bytes(16)),
			'nbf' => $now,
			'exp' => $now + 600,
			'iat' => $now,
		];

		$header = [
			'alg' => 'RS256',
			'typ' => 'JWT',
			'x5t' => $this->certThumbprint,
		];

		$segments = [
			$this->base64UrlEncode(json_encode($header)),
			$this->base64UrlEncode(json_encode($payload)),
		];

		$dataToSign = implode('.', $segments);

		$signature = '';
		$ok = openssl_sign($dataToSign, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
		if (!$ok) {
			Logger::getInstance()->log("JWT signing failed.");
			throw new Exception("JWT signing for client assertion failed.");
		}

		$segments[] = $this->base64UrlEncode($signature);
		return implode('.', $segments);
	}

	/**
	 * Performs Base64-URL encoding.
	 *
	 * @param string $data
	 * @return string
	 */
	private function base64UrlEncode(string $data): string
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	/**
	 * Example method: Retrieves organization information.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getOrganizationInfo(): array
	{
		$token = $this->getAccessToken();
		$url = "https://graph.microsoft.com/v1.0/organization";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				"Authorization: Bearer $token",
				"Accept: application/json",
			],
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false || $httpCode !== 200) {
			throw new Exception("Error retrieving organization data ($httpCode): $response");
		}

		$data = json_decode($response, true);
		if (!isset($data['value'][0])) {
			throw new Exception("Invalid response structure from /organization");
		}

		$org = $data['value'][0];

		return [
			'tenantId' => $org['id'] ?? null,
			'displayName' => $org['displayName'] ?? null,
			'defaultDomain' => $org['verifiedDomains'][0]['name'] ?? null,
		];
	}
}
