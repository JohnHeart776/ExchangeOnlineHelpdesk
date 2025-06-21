<?php

class AzureAiResponse
{
	public array $choices;
	public int $created;
	public string $id;
	public string $model;
	public string $object;
	public array $prompt_filter_results;
	public string $system_fingerprint;
	public AzureAiReponseUsage $usage;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->choices = array_map(fn($choice) => AzureAiResponseChoice::fromArray($choice), $data['choices']);
		$instance->created = $data['created'];
		$instance->id = $data['id'];
		$instance->model = $data['model'];
		$instance->object = $data['object'];
		$instance->prompt_filter_results = array_map(fn($result) => PromptFilterResult::fromArray($result), $data['prompt_filter_results']);
		$instance->system_fingerprint = $data['system_fingerprint'];
		$instance->usage = AzureAiReponseUsage::fromArray($data['usage']);
		return $instance;
	}

	/**
	 * @return AzureAiResponseChoice[]
	 */
	public function getChoices(): array
	{
		return $this->choices;
	}

	public function getFirstChoice(): AzureAiResponseChoice
	{
		return $this->choices[0];
	}

	public function getFirstContent(): string
	{
		return $this->getFirstChoice()->message->content;
	}
}

class AzureAiResponseChoice
{
	public ContentFilterResults $content_filter_results;
	public string $finish_reason;
	public int $index;
	public ?string $logprobs;
	public AzureAiResponseMessage $message;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->content_filter_results = ContentFilterResults::fromArray($data['content_filter_results']);
		$instance->finish_reason = $data['finish_reason'];
		$instance->index = $data['index'];
		$instance->logprobs = $data['logprobs'];
		$instance->message = AzureAiResponseMessage::fromArray($data['message']);
		return $instance;
	}

	/**
	 * @return AzureAiResponseMessage
	 */
	public function getMessage(): AzureAiResponseMessage
	{
		return $this->message;
	}
}

class ContentFilterResults
{
	public FilterResult $hate;
	public ProtectedMaterial $protected_material_code;
	public ProtectedMaterial $protected_material_text;
	public FilterResult $self_harm;
	public FilterResult $sexual;
	public FilterResult $violence;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->hate = FilterResult::fromArray($data['hate']);
		$instance->self_harm = FilterResult::fromArray($data['self_harm']);
		$instance->sexual = FilterResult::fromArray($data['sexual']);
		$instance->violence = FilterResult::fromArray($data['violence']);
		return $instance;
	}
}

class FilterResult
{
	public bool $filtered;
	public string $severity;

	public static function fromArray(?array $data): self
	{
		if (!$data)
			return new self();

		$instance = new self();
		$instance->filtered = $data['filtered'];
		$instance->severity = $data['severity'];
		return $instance;
	}
}

class ProtectedMaterial
{
	public bool $filtered;
	public bool $detected;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->filtered = $data['filtered'];
		$instance->detected = $data['detected'];
		return $instance;
	}
}

class AzureAiResponseMessage
{
	public array $annotations;
	public string $content;
	public ?string $refusal;
	public string $role;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->annotations = $data['annotations'];
		$instance->content = $data['content'];
		$instance->refusal = $data['refusal'];
		$instance->role = $data['role'];
		return $instance;
	}

	public function getContent()
	{
		return $this->content;
	}
}

class PromptFilterResult
{
	public int $prompt_index;
	public array $content_filter_results;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->prompt_index = $data['prompt_index'];

		if (isset($data['content_filter_results']))
			$instance->content_filter_results = $data['content_filter_results'];

		return $instance;
	}
}

class AzureAiReponseUsage
{
	public int $completion_tokens;
	public CompletionTokensDetails $completion_tokens_details;
	public int $prompt_tokens;
	public PromptTokensDetails $prompt_tokens_details;
	public int $total_tokens;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->completion_tokens = $data['completion_tokens'];
		$instance->completion_tokens_details = CompletionTokensDetails::fromArray($data['completion_tokens_details']);
		$instance->prompt_tokens = $data['prompt_tokens'];
		$instance->prompt_tokens_details = PromptTokensDetails::fromArray($data['prompt_tokens_details']);
		$instance->total_tokens = $data['total_tokens'];
		return $instance;
	}
}

class CompletionTokensDetails
{
	public int $accepted_prediction_tokens;
	public int $audio_tokens;
	public int $reasoning_tokens;
	public int $rejected_prediction_tokens;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->accepted_prediction_tokens = $data['accepted_prediction_tokens'];
		$instance->audio_tokens = $data['audio_tokens'];
		$instance->reasoning_tokens = $data['reasoning_tokens'];
		$instance->rejected_prediction_tokens = $data['rejected_prediction_tokens'];
		return $instance;
	}
}

class PromptTokensDetails
{
	public int $audio_tokens;
	public int $cached_tokens;

	public static function fromArray(array $data): self
	{
		$instance = new self();
		$instance->audio_tokens = $data['audio_tokens'];
		$instance->cached_tokens = $data['cached_tokens'];
		return $instance;
	}
}
