<?php

class AzureAiAuthenticator
{
	private string $endpoint;
	private string $apiKey;
	private string $apiVersion;

	/**
	 * @param string $endpoint   Base URL of the Azure OpenAI endpoint (e.g., https://your-resource.openai.azure.com)
	 * @param string $apiKey     API key for authentication
	 * @param string $apiVersion API version to use (default: 2023-05-15)
	 */
	public function __construct(
		string $endpoint,
		string $apiKey,
		string $apiVersion = '2023-05-15')
	{
		$this->endpoint = rtrim($endpoint, '/');
		$this->apiKey = $apiKey;
		$this->apiVersion = $apiVersion;
	}

	public static function getDefault(): self
	{
		return new self (
			Config::get("ai.azure.model.endpoint"),
			Config::get("ai.azure.api.key"),
			Config::get("ai.azure.api.version", "2023-05-15")
		);
	}

	/**
	 * Get headers required for Azure OpenAI requests
	 *
	 * @return array<string>
	 * @throws Exception if the API key is missing
	 */
	public function getHeaders(): array
	{
		if (empty($this->apiKey)) {
			throw new Exception('API key is missing.');
		}
		return [
			'Content-Type: application/json',
			'api-key: ' . $this->apiKey,
		];
	}

	/**
	 * Get the base endpoint URL
	 */
	public function getEndpoint(): string
	{
		return $this->endpoint;
	}

	/**
	 * Get the API version
	 */
	public function getApiVersion(): string
	{
		return $this->apiVersion;
	}
}
