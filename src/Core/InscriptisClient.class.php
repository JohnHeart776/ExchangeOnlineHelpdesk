<?php

class InscriptisClient
{
	/**
	 * URL of the Inscriptis service.
	 *
	 * @var string
	 */
	private $serviceUrl;

	/**
	 * Constructor.
	 *
	 * @param string $domain  The domain of the Inscriptis service, e.g. "localhost:5000".
	 * @param bool   $useHttp Optional: true for HTTP, false (default) for HTTPS.
	 */
	public function __construct(string $domain, bool $useHttp = false)
	{
		$protocol = $useHttp ? 'http' : 'https';
		$this->serviceUrl = $protocol . '://' . $domain . '/get_text';
	}

	/**
	 * Converts an HTML string to plain text using the Inscriptis service.
	 *
	 * @param string $html The HTML string to be converted.
	 * @return string The resulting text.
	 * @throws Exception If an error occurs during the request or processing.
	 */
	public function convertHtmlToText(string $html): string
	{
		$ch = curl_init();

		if ($ch === false) {
			throw new Exception('cURL could not be initialized.');
		}

		// cURL-Optionen setzen
		curl_setopt($ch, CURLOPT_URL, $this->serviceUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Content-Type: text/html; encoding=UTF8",
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $html);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in Sekunden

		// Anfrage ausführen
		$response = curl_exec($ch);

		// Fehlerüberprüfung
		if ($response === false) {
			$errorMsg = curl_error($ch);
			curl_close($ch);
			throw new Exception("cURL-Fehler: " . $errorMsg);
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode !== 200) {
			throw new Exception("HTTP error: Code {$httpCode}. Response: " . $response);
		}

		return $response;
	}

}
