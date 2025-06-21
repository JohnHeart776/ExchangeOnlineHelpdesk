<?php

class AzureAiClient implements AiClientInterface
{

	private string $deployment;
	private string $systemMessage = "";
	private bool $useLocalAiCache = false;

	public function __construct(
		private AzureAiAuthenticator $authenticator
	)
	{
		$this->deployment = Config::getConfigValueFor('ai.azure.model.deployment', 'default');
		$this->systemMessage = Config::getConfigValueFor('ai.prompt.system', '');
		$this->useLocalAiCache = Config::getConfigValueFor('ai.azure.cache.enabled', false);
	}

	/**
	 * @param array $messages
	 * @return AzureAiResponse
	 * @throws Exception
	 */
	private function getResponse(array $messages): AzureAiResponse
	{
		$url = sprintf(
			'%s/openai/deployments/%s/chat/completions?api-version=%s',
			$this->authenticator->getEndpoint(),
			$this->deployment,
			$this->authenticator->getApiVersion()
		);

		$body = ['messages' => $messages];

		$payload = json_encode($body);
		if ($payload === false) {
			throw new Exception('Failed to encode request payload.');
		}
		$payloadHashBase = __CLASS__ . '_||__||_' . $payload . '_||__||_' . serialize($this);
		$payloadHash = hash('sha256', $payloadHashBase);

		$aiCache = null;
		if ($this->useLocalAiCache) {
			$aiCache = AiCacheController::searchOneBy('PayloadHash', $payloadHash);
		}

		if (!$aiCache) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authenticator->getHeaders());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

			$response = curl_exec($ch);
			if ($response === false) {
				$error = curl_error($ch);
				curl_close($ch);
				throw new Exception('Curl error: ' . $error);
			}

			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($status < 200 || $status >= 300) {
				throw new Exception('Unexpected HTTP status: ' . $status . ' Response: ' . $response);
			}

			if ($this->useLocalAiCache) {
				$newAiCache = new AiCache(0);
				$newAiCache->PayloadHash = $payloadHash;
				$newAiCache->Payload = $payload;
				$newAiCache->Response = $response;
				$aiCache = AiCacheController::save($newAiCache);
			}
		}

		$responseData = $aiCache ? $aiCache->getResponse() : $response;
		$decoded = json_decode($responseData, true);
		if ($decoded === null) {
			throw new Exception('Failed to decode JSON response: ' . json_last_error_msg());
		}

		return AzureAiResponse::fromArray($decoded);
	}


	/**
	 * Get an answer from Azure OpenAI chat completion
	 *
	 * @param string      $deployment    Deployment or model name
	 * @param string      $userPrompt    The user's message/question
	 * @param string|null $systemMessage System message defining the AI's behavior
	 * @param array       $options       Additional options for the API request
	 * @return string The AI's response
	 * @throws Exception on API or processing errors
	 */
	public function getResponseString(
		string $userPrompt,
	): string
	{
		$messages = [];

		if ($this->systemMessage !== '') {
			$messages[] = $this->getSystemRole();
		}

		$messages[] = [
			'role' => 'user',
			'content' => $userPrompt,
		];

		try {
			$response = $this->getResponse($messages);
			return $response->getFirstChoice()->getMessage()->getContent() ?? '<keine Antwort>';
		} catch (Exception $e) {
			throw new Exception("Failed to get answer: " . $e->getMessage());
		}
	}

	public function getSystemRole(): array
	{
		return [
			'role' => 'system',
			'content' => $this->systemMessage,
		];
	}

	public function getInitialPayload(bool $includeSystemRole = true): array
	{
		$a = [];
		if ($includeSystemRole) {
			$a[] = $this->getSystemRole();
		}
		return $a;
	}

	public function getResponseForMessageArray(array $messages)
	{
		$url = sprintf(
			'%s/openai/deployments/%s/chat/completions?api-version=%s',
			$this->authenticator->getEndpoint(),
			$this->deployment,
			$this->authenticator->getApiVersion()
		);

		$body = ['messages' => $messages];
		$payload = json_encode($body);
		if ($payload === false) {
			throw new Exception('Failed to encode request payload.');
		}
		$payloadHashBase = __CLASS__ . '_||__||_' . $payload . '_||__||_' . serialize($this);
		$payloadHash = hash('sha256', $payloadHashBase);

		$aiCache = null;
		if ($this->useLocalAiCache) {
			$aiCache = AiCacheController::searchOneBy('PayloadHash', $payloadHash);
		}

		if (!$aiCache) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authenticator->getHeaders());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

			$response = curl_exec($ch);
			if ($response === false) {
				$error = curl_error($ch);
				curl_close($ch);
				throw new Exception('Curl error: ' . $error);
			}

			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($status < 200 || $status >= 300) {
				throw new Exception('Unexpected HTTP status: ' . $status . ' Response: ' . $response);
			}

			if ($this->useLocalAiCache) {
				$newAiCache = new AiCache(0);
				$newAiCache->PayloadHash = $payloadHash;
				$newAiCache->Payload = $payload;
				$newAiCache->Response = $response;
				$aiCache = AiCacheController::save($newAiCache);
			}
		}

		$responseData = $aiCache ? $aiCache->getResponse() : $response;
		$decoded = json_decode($responseData, true);
		if ($decoded === null) {
			throw new Exception('Failed to decode JSON response: ' . json_last_error_msg());
		}

		return AzureAiResponse::fromArray($decoded);
	}
}
