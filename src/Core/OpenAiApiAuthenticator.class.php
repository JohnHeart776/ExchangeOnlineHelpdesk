<?php

class OpenAiApiAuthenticator
{
	private string $ApiVersion = 'v1';
	private string $ApiBaseUrl = 'https://api.openai.com';

	private string $apiKey;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * Liefert einen Standard-Instanz mit dem API-Key aus der Konfiguration.
	 */
	public static function getDefault(): OpenAiApiAuthenticator
	{
		return new self(Config::getConfigValueFor('ai.openai.api.secret'));
	}

	/**
	 * Gibt die HTTP-Header für die API-Anfrage zurück.
	 *
	 * @return array
	 */
	public function getAuthenticationHeaders(): array
	{
		return [
			"Authorization: Bearer {$this->apiKey}",
			"Content-Type: application/json",
		];
	}

	/**
	 * Gibt den rohen API-Key zurück.
	 */
	public function getApiKey(): string
	{
		return $this->apiKey;
	}

	/**
	 * Basis-URL der OpenAI API, inkl. Version.
	 *
	 * @return string
	 */
	public function getBaseUrl(): string
	{
		return rtrim($this->ApiBaseUrl, '/') . '/' . $this->ApiVersion;
	}
}
