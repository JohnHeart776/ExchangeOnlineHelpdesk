<?php

class AiService
{

	public static function getRepsonse(string $userPrompt): string
	{

		$client = self::getClient();
		if (!$client) {
			throw new Exception("Invalid AI Client obtained");
		}

		return $client->getResponseString($userPrompt);
	}

	public static function getClient(): ?AiClientInterface
	{
		$aiEnabled = Config::get("ai.enable");
		if (!$aiEnabled) {
			return null;
		}

		$vendor = Config::get("ai.vendor");
		switch ($vendor) {
			case "azure":
				return new AzureAiClient(AzureAiAuthenticator::getDefault());
				break;
			case "openai":
				return new OpenAiClient(OpenAiApiAuthenticator::getDefault());
				break;
			default:
				return null;
		}
	}

	public static function createPayloadElement(string $role, array $content)
	{
		return [
			'role' => $role,
			'content' => $content,
		];
	}

}
