<?php

class NounProjectClient
{
	private string $key;
	private string $secret;
	private string $baseUrl = 'https://api.thenounproject.com/v2';

	public function __construct(string $key, string $secret)
	{
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * Searches for the first icon matching a search term and downloads it as a raw string (image bytes).
	 *
	 * @param string $term   The search term
	 * @param string $format "png" or "svg"
	 * @param int    $size   For PNG: image size (20–1200). Ignored for SVG.
	 * @return string        Raw icon data as binary string
	 * @throws RuntimeException with debug info; for PNG fallback to thumbnail_url
	 */
	public function fetchIcon(string $term, string $format = 'png', int $size = 1200): string
	{
		// --- 1) SEARCH ---
		$searchUrl = "{$this->baseUrl}/icon";
		$searchParams = ['query' => $term, 'limit' => 1];
		if ($format === 'svg') {
			$searchParams['include_svg'] = 1;
		}

		try {
			$searchJson = $this->request('GET', $searchUrl, $searchParams);
		} catch (\RuntimeException $e) {
			throw new \RuntimeException("SEARCH-STEP FAILED: {$e->getMessage()}");
		}

		try {
			$searchData = json_decode($searchJson, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $je) {
			throw new \RuntimeException(
				"JSON-DECODE ERROR in SEARCH-STEP: {$je->getMessage()}\n"
				. "Raw response:\n{$searchJson}"
			);
		}

		if (empty($searchData['icons'][0])) {
			throw new \RuntimeException(
				"NO ICON FOUND for term “{$term}”.\n"
				. "Search response:\n{$searchJson}"
			);
		}
		$icon = $searchData['icons'][0];

		// --- 2a) SVG branch ---
		if ($format === 'svg') {
			if (!empty($icon['icon_url'])) {
				return $this->fetchUrl($icon['icon_url']);
			}
			if (!empty($icon['thumbnail_url'])) {
				return $this->fetchUrl($icon['thumbnail_url']);
			}
			throw new \RuntimeException(
				"SVG not available (only public domain icons provide icon_url in free plan)."
			);
		}

		// --- 2b) PNG branch (mit color-Parameter) ---
		if ($format === 'png') {
			$downloadUrl = "{$this->baseUrl}/icon/{$icon['id']}/download";
			$downloadParams = [
				'filetype' => 'png',
				'size' => $size,
				'color' => '000000',  // Default-Hexfarbe Schwarz
			];

			try {
				$downloadJson = $this->request('GET', $downloadUrl, $downloadParams);
			} catch (\RuntimeException $e) {
				// Falls 403: kein Download-Recht, dann Fallback auf thumbnail_url
				if (strpos($e->getMessage(), 'HTTP ERROR 403') !== false && !empty($icon['thumbnail_url'])) {
					return $this->fetchUrl($icon['thumbnail_url']);
				}
				throw new \RuntimeException("DOWNLOAD-STEP FAILED: {$e->getMessage()}");
			}

			try {
				$dl = json_decode($downloadJson, true, 512, JSON_THROW_ON_ERROR);
			} catch (\JsonException $je) {
				throw new \RuntimeException(
					"JSON-DECODE ERROR in DOWNLOAD-STEP: {$je->getMessage()}\n"
					. "Raw response:\n{$downloadJson}"
				);
			}

			if (empty($dl['base64_encoded_file'])) {
				throw new \RuntimeException(
					"No base64_encoded_file in download response.\n"
					. "Download response:\n{$downloadJson}"
				);
			}

			return base64_decode($dl['base64_encoded_file']);
		}

		throw new \InvalidArgumentException('Format must be "png" or "svg".');
	}

	/**
	 * Führt einen OAuth-authentifizierten HTTP-Request durch.
	 * Bei HTTP 400 wird das JSON-Body-Feld "message" in der Exception ausgeworfen.
	 *
	 * @throws RuntimeException
	 */
	private function request(string $method, string $url, array $queryParams = []): string
	{
		$authHeader = $this->buildOAuthHeader($method, $url, $queryParams);
		$fullUrl = $url . (empty($queryParams) ? '' : ('?' . http_build_query($queryParams)));

		$ch = curl_init($fullUrl);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				"Authorization: {$authHeader}",
				'Accept: application/json',
			],
		]);
		$body = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlErr = curl_error($ch);
		curl_close($ch);

		if ($httpCode === 400) {
			$apiMessage = $body;
			if ($body !== false) {
				try {
					$err = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
					$apiMessage = $err['message'] ?? $body;
				} catch (\JsonException $je) {
					// fallback to raw body
				}
			}
			throw new \RuntimeException("HTTP 400 Bad Request: {$apiMessage}");
		}

		if ($body === false || $httpCode < 200 || $httpCode >= 300) {
			$debug = "HTTP ERROR {$httpCode}";
			if ($curlErr) {
				$debug .= " | cURL error: {$curlErr}";
			}
			$debug .= "\nREQUEST METHOD: {$method}";
			$debug .= "\nREQUEST URL: {$fullUrl}";
			$debug .= "\nQUERY PARAMS: " . json_encode($queryParams);
			$debug .= "\nAUTH HEADER: {$authHeader}";
			$debug .= "\nRESPONSE BODY:\n{$body}\n";
			throw new \RuntimeException($debug);
		}

		return $body;
	}

	/**
	 * Holt eine öffentliche URL ohne Authentifizierung (für icon_url/thumbnail_url).
	 *
	 * @throws RuntimeException
	 */
	private function fetchUrl(string $url): string
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
		]);
		$body = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlErr = curl_error($ch);
		curl_close($ch);

		if ($body === false || $httpCode !== 200) {
			$debug = "FAILED TO FETCH URL ({$httpCode})";
			if ($curlErr) {
				$debug .= " | cURL error: {$curlErr}";
			}
			$debug .= "\nURL: {$url}";
			throw new \RuntimeException($debug);
		}

		return $body;
	}

	/**
	 * Baut den OAuth-1.0a Authorization-Header nach RFC 5849.
	 */
	private function buildOAuthHeader(string $method, string $url, array $params): string
	{
		$oauth = [
			'oauth_consumer_key' => $this->key,
			'oauth_nonce' => bin2hex(random_bytes(16)),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0',
		];

		$all = [];
		foreach (array_merge($oauth, $params) as $k => $v) {
			$all[rawurlencode($k)] = rawurlencode((string)$v);
		}
		ksort($all);

		$pairs = [];
		foreach ($all as $k => $v) {
			$pairs[] = "{$k}={$v}";
		}
		$paramString = implode('&', $pairs);

		$baseString = strtoupper($method) . '&'
			. rawurlencode($url) . '&'
			. rawurlencode($paramString);

		$signingKey = rawurlencode($this->secret) . '&';
		$signature = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
		$oauth['oauth_signature'] = $signature;

		$headerParts = [];
		foreach ($oauth as $k => $v) {
			$headerParts[] = rawurlencode($k) . '="' . rawurlencode($v) . '"';
		}
		return 'OAuth ' . implode(', ', $headerParts);
	}
}
