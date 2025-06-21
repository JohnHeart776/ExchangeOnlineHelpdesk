<?php

class InscriptisClient
{
	/**
	 * URL des Inscriptis-Services.
	 *
	 * @var string
	 */
	private $serviceUrl;

	/**
	 * Konstruktor.
	 *
	 * @param string $domain  Die Domain des Inscriptis-Services, z. B. "localhost:5000".
	 * @param bool   $useHttp Optional: true für HTTP, false (Standard) für HTTPS.
	 */
	public function __construct(string $domain, bool $useHttp = false)
	{
		$protocol = $useHttp ? 'http' : 'https';
		$this->serviceUrl = $protocol . '://' . $domain . '/get_text';
	}

	/**
	 * Wandelt einen HTML-String in reinen Text um, indem der Inscriptis-Service verwendet wird.
	 *
	 * @param string $html Der HTML-String, der konvertiert werden soll.
	 * @return string Der resultierende Text.
	 * @throws Exception Falls ein Fehler bei der Anfrage oder der Verarbeitung auftritt.
	 */
	public function convertHtmlToText(string $html): string
	{
		$ch = curl_init();

		if ($ch === false) {
			throw new Exception('cURL konnte nicht initialisiert werden.');
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
			throw new Exception("HTTP-Fehler: Code {$httpCode}. Antwort: " . $response);
		}

		return $response;
	}

}
