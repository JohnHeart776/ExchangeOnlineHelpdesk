<?php

class IconFinderClient
{
	/**
	 * Iconfinder API Endpoint
	 * @var string
	 */
	private $apiBase = 'https://api.iconfinder.com/v4';

	/**
	 * Client ID (optional)
	 * @var string
	 */
	private $clientId;

	/**
	 * API Key / Access Token
	 * @var string
	 */
	private $apiKey;

	/**
	 * Constructor
	 *
	 * @param string $clientId
	 * @param string $apiKey
	 */
	public function __construct(string $clientId, string $apiKey)
	{
		$this->clientId = $clientId;
		$this->apiKey = $apiKey;
	}

	/**
	 * Searches for free icons and downloads the first one.
	 *
	 * @param string $searchTerm Search term
	 * @return string Icon data as blob
	 * @throws Exception on errors
	 */
	public function getFirstFreeIconBlob(string $searchTerm): string
	{
		// 1. Search for first free icon
		$searchUrl = $this->apiBase . '/icons/search'
			. '?query=' . urlencode($searchTerm)
			. '&count=1'
			. '&price=free';

		$response = $this->httpRequest('GET', $searchUrl);
		$data = json_decode($response, true);

		if (empty($data['icons'][0])) {
			throw new Exception('No free icon found for: ' . $searchTerm);
		}

		$icon = $data['icons'][0];

		// 2. Select a raster size (here: first available)
		if (empty($icon['raster_sizes'][0]['formats'])) {
			throw new Exception('No raster formats found for the icon.');
		}

		$formats = $icon['raster_sizes'][0]['formats'];
		$downloadUrl = null;

		// Look for PNG format, otherwise use first format
		foreach ($formats as $fmt) {
			if (isset($fmt['format']) && strtolower($fmt['format']) === 'png' && isset($fmt['download_url'])) {
				$downloadUrl = $fmt['download_url'];
				break;
			}
		}

		if (!$downloadUrl) {
			// Fallback: first format
			$downloadUrl = $formats[0]['download_url'] ?? null;
		}

		if (!$downloadUrl) {
			throw new Exception('No download link found for the icon.');
		}

		// 3. Download the icon
		return $this->httpRequest('GET', $downloadUrl, [], false);
	}

	/**
	 * Executes an HTTP request via cURL.
	 *
	 * @param string $method GET or POST
	 * @param string $url
	 * @param array  $body   associative array for POST data
	 * @return string Raw response or JSON string
	 * @throws Exception on HTTP error
	 */
	private function httpRequest(string $method, string $url, array $body = []): string
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Authorization: Bearer ' . $this->apiKey,
			'Content-Type: application/json',
		]);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		}

		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (curl_errno($ch)) {
			$err = curl_error($ch);
			curl_close($ch);
			throw new Exception('cURL error: ' . $err);
		}

		curl_close($ch);

		if ($status < 200 || $status >= 300) {
			throw new Exception("HTTP request failed with status $status: $result");
		}

		return $result;
	}
}
