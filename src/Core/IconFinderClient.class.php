<?php

class IconFinderClient
{
	/**
	 * Iconfinder API Endpunkt
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
	 * Konstruktor
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
	 * Sucht nach kostenlosen Icons und lädt das erste herunter.
	 *
	 * @param string $searchTerm Suchbegriff
	 * @return string Icon-Daten als Blob
	 * @throws Exception bei Fehlern
	 */
	public function getFirstFreeIconBlob(string $searchTerm): string
	{
		// 1. Suche das erste kostenlose Icon
		$searchUrl = $this->apiBase . '/icons/search'
			. '?query=' . urlencode($searchTerm)
			. '&count=1'
			. '&price=free';

		$response = $this->httpRequest('GET', $searchUrl);
		$data = json_decode($response, true);

		if (empty($data['icons'][0])) {
			throw new Exception('Kein kostenloses Icon gefunden für: ' . $searchTerm);
		}

		$icon = $data['icons'][0];

		// 2. Wähle eine Rastergröße (hier: erste verfügbare)
		if (empty($icon['raster_sizes'][0]['formats'])) {
			throw new Exception('Keine Raster-Formate für das Icon gefunden.');
		}

		$formats = $icon['raster_sizes'][0]['formats'];
		$downloadUrl = null;

		// Suche PNG-Format, sonst erstes Format
		foreach ($formats as $fmt) {
			if (isset($fmt['format']) && strtolower($fmt['format']) === 'png' && isset($fmt['download_url'])) {
				$downloadUrl = $fmt['download_url'];
				break;
			}
		}

		if (!$downloadUrl) {
			// Fallback: erstes Format
			$downloadUrl = $formats[0]['download_url'] ?? null;
		}

		if (!$downloadUrl) {
			throw new Exception('Kein Download-Link gefunden für das Icon.');
		}

		// 3. Lade das Icon herunter
		return $this->httpRequest('GET', $downloadUrl, [], false);
	}

	/**
	 * Führt eine HTTP-Anfrage per cURL aus.
	 *
	 * @param string $method GET oder POST
	 * @param string $url
	 * @param array  $body   assoziatives Array für POST-Daten
	 * @return string Raw-Antwort oder JSON-String
	 * @throws Exception bei HTTP-Fehler
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
