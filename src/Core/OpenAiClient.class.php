<?php

class OpenAiClient implements AiClientInterface
{

	protected string $model;
	protected ?string $baselinePrompt = null;

	/** Proxy-/Cache-Settings **/
	protected bool $useProxy;
	protected ?string $proxyUrl;

	/**
	 * @param OpenAiApiAuthenticator $authenticator
	 * @param bool|null $useLocalAiCache (local AI cache)
	 * @param bool|null              $appendBaselinePromptToPrompt
	 * @throws \Database\DatabaseQueryException
	 */
	public function __construct(
		private OpenAiApiAuthenticator $authenticator,
		private ?bool                  $useLocalAiCache = true,
		private ?bool                  $appendBaselinePromptToPrompt = true
	)
	{

		$this->model = Config::getConfigValueFor('ai.openai.model', 'gpt-3.5-turbo');

		// externe Proxy-/Cache-URL aus Config
		$this->useProxy = (bool)Config::getConfigValueFor('ai.openai.proxy.enable', false);
		$this->proxyUrl = $this->useProxy
			? rtrim(Config::getConfigValueFor('ai.openai.proxy.url', ''), '/')
			: null;
	}

	/**
	 * Sends the userPrompt (plus optional baseline prompt) and options
	 * to the OpenAI API or to the optional proxy script.
	 *
	 * @param string $userPrompt
	 * @param array  $options Additional parameters (e.g. temperature, max_tokens)
	 * @return \Struct\OpenAiReponse|null
	 * @throws Exception
	 */
	public function getResponse(string $userPrompt): ?\Struct\OpenAiReponse
	{
		if (!Config::getConfigValueFor('ai.enable')) {
			return null;
		}

		// Relative endpoint
		$relativeEndpoint = '/v1/chat/completions';

		// Choose URL: external cache/proxy or direct API call
		if ($this->useProxy) {
			if (!$this->proxyUrl) {
				throw new \RuntimeException('Proxy enabled but openai.cache.url not set.');
			}
			$apiUrl = $this->proxyUrl . '?endpoint=' . $relativeEndpoint;
		} else {
			$apiUrl = $this->authenticator->getBaseUrl() . $relativeEndpoint;
		}

		// Prompt zusammenbauen
		$prompt = '';
		if ($this->baselinePrompt) {
			$prompt .= $this->baselinePrompt . PHP_EOL . PHP_EOL;
		}
		$prompt .= $userPrompt;

		// Basis-Payload
		$payload = [
			'model' => $this->model,
			'messages' => [],
		];

		if ($this->appendBaselinePromptToPrompt) {
			$payload['messages'][] = $this->getSystemRole();
		}

		$payload['messages'][] = [
			'role' => 'user',
			'content' => $prompt,
		];

		// JSON and hash for local cache
		$payloadJson = json_encode($payload);
		$payloadHash = hash('sha256', __CLASS__ . '--' . $payloadJson);

		$aiCache = null;
		if ($this->useLocalAiCache) {
			$aiCache = AiCacheController::searchOneBy('PayloadHash', $payloadHash);
		}

		// Request only if not in local cache
		if (!$aiCache) {
			$headers = $this->authenticator->getAuthenticationHeaders();
			$ch = curl_init($apiUrl);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_POSTFIELDS => $payloadJson,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_SSL_VERIFYPEER => true,
			]);

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($httpCode === 200 && $response) {
				$newAiCache = new AiCache(0);
				$newAiCache->PayloadHash = $payloadHash;
				$newAiCache->Payload = $payloadJson;
				$newAiCache->Response = $response;
				$aiCache = AiCacheController::save($newAiCache);
			}
		}

		if (!$aiCache) {
			return null;
		}

		return \Struct\OpenAiReponse::fromOpenAiJsonString($aiCache->getResponse());
	}

	/**
	 * Returns the currently used model.
	 *
	 * @return string
	 */
	public function getModel(): string
	{
		return $this->model;
	}

	/**
	 * Returns the baseline prompt (if set).
	 *
	 * @return string|null
	 */
	public function getBaselinePrompt(): ?string
	{
		return $this->baselinePrompt;
	}

	public function getResponseString(string $userPrompt): string
	{
		return $this->getResponse($userPrompt)->getFirstContent();
	}

	public function getSystemRole(): array
	{
		return [
			'role' => 'system',
			'content' => Config::getConfigValueFor('ai.prompt.system', ''),
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
		if (!Config::getConfigValueFor('ai.enable')) {
			return null;
		}

		// Relative endpoint
		$relativeEndpoint = '/v1/chat/completions';

		if ($this->useProxy) {
			if (!$this->proxyUrl) {
				throw new \RuntimeException('AI Proxy enabled but no openai.cache.url set.');
			}
			$apiUrl = $this->proxyUrl . '?endpoint=' . $relativeEndpoint;
		} else {
			$apiUrl = $this->authenticator->getBaseUrl() . $relativeEndpoint;
		}

		$payload = [
			'model' => $this->model,
			'messages' => $messages,
		];
		// JSON and hash for local cache
		$payloadJson = json_encode($payload);
		$payloadHash = hash('sha256', __CLASS__ . '--' . $payloadJson);

		$aiCache = null;
		if ($this->useLocalAiCache) {
			$aiCache = AiCacheController::searchOneBy('PayloadHash', $payloadHash);
		}

		// Request only if not in local cache
		if (!$aiCache) {
			$headers = $this->authenticator->getAuthenticationHeaders();
			$ch = curl_init($apiUrl);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_POSTFIELDS => $payloadJson,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_SSL_VERIFYPEER => true,
			]);

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($httpCode === 200 && $response) {
				$newAiCache = new AiCache(0);
				$newAiCache->PayloadHash = $payloadHash;
				$newAiCache->Payload = $payloadJson;
				$newAiCache->Response = $response;
				$aiCache = AiCacheController::save($newAiCache);
			}
		}

		if (!$aiCache) {
			return null;
		}

		return \Struct\OpenAiReponse::fromOpenAiJsonString($aiCache->getResponse());
	}
}
