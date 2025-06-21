<?php


class CurlHelper
{
	public static function get(string $url, array $headers = []): string
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
		]);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!$response || $code >= 400) {
			throw new \Exception("GET $url fehlgeschlagen: ($code) $err\n$response");
		}

		return $response;
	}

	public static function post(string $url, array $headers = [], string|array $body = ""): string
	{
		if (is_array($body)) {
			$body = http_build_query($body);
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => $headers,
		]);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!$response || $code >= 400) {
			throw new \Exception("POST $url fehlgeschlagen: ($code) $err\n$response");
		}

		return $response;
	}

	public static function postJson(string $url, array $data, array $headers = []): string
	{
		$ch = curl_init($url);
		$payload = json_encode($data);

		$defaultHeaders = [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($payload),
		];

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);

		if ($response === false || $httpCode >= 400) {
			throw new \Exception("POST-Request an $url fehlgeschlagen ($httpCode): $error\nAntwort: $response");
		}

		return $response;
	}

	/**
	 * Führt eine PATCH-Anfrage mit JSON-Payload aus.
	 *
	 * @param string $url     Die URL für die PATCH-Anfrage.
	 * @param array  $data    Die Daten, die aktualisiert werden sollen.
	 * @param array  $headers Zusätzliche HTTP-Header (optional).
	 * @return string
	 * @throws \Exception Falls die Anfrage fehlschlägt.
	 */
	public static function patchJson(string $url, array $data, array $headers = []): string
	{
		$ch = curl_init($url);
		$payload = json_encode($data);

		$defaultHeaders = [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($payload),
		];

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => 'PATCH',
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);

		if ($response === false || $httpCode >= 400) {
			throw new \Exception("PATCH-Request an $url fehlgeschlagen ($httpCode): $error\nAntwort: $response");
		}

		return $response;
	}

}
