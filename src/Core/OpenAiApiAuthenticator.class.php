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
	 * Returns a default instance with the API key from the configuration.
	 */
	public static function getDefault(): OpenAiApiAuthenticator
	{
		return new self(Config::getConfigValueFor('ai.openai.api.secret'));
	}

	/**
	 * Returns the HTTP headers for the API request.
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
	 * Returns the raw API key.
	 */
	public function getApiKey(): string
	{
		return $this->apiKey;
	}

	/**
	 * Base URL of the OpenAI API, including version.
	 *
	 * @return string
	 */
	public function getBaseUrl(): string
	{
		return rtrim($this->ApiBaseUrl, '/') . '/' . $this->ApiVersion;
	}
}
