<?php

namespace Struct;

class OpenAiReponse
{
	/** @var string */
	public string $id;

	/** @var string */
	public string $object;

	/** @var int */
	public int $created;

	/** @var string */
	public string $model;

	/** @var array */
	public array $choices;

	/** @var array */
	public array $usage;

	/** @var string|null */
	public ?string $service_tier;

	/** @var mixed|null */
	public $system_fingerprint;

	/**
	 * Constructor for setting the values
	 */
	public function __construct(
		string  $id,
		string  $object,
		int     $created,
		string  $model,
		array   $choices,
		array   $usage,
		?string $service_tier,
				$system_fingerprint
	)
	{
		$this->id = $id;
		$this->object = $object;
		$this->created = $created;
		$this->model = $model;
		$this->choices = $choices;
		$this->usage = $usage;
		$this->service_tier = $service_tier;
		$this->system_fingerprint = $system_fingerprint;
	}

	/**
	 * Creates an OpenAiReponse object from an OpenAI JSON string (or decoded array)
	 *
	 * @param string|array $json
	 * @return static
	 * @throws \Exception
	 */
	public static function fromOpenAiJsonString($json): self
	{
		if (is_string($json)) {
			$data = json_decode($json, true);
			if ($data === null) {
 			throw new \InvalidArgumentException("Invalid JSON string provided.");
			}
		} else {
			$data = $json;
		}

		return new self(
			$data['id'] ?? '',
			$data['object'] ?? '',
			$data['created'] ?? 0,
			$data['model'] ?? '',
			$data['choices'] ?? [],
			$data['usage'] ?? [],
			$data['service_tier'] ?? null,
			$data['system_fingerprint'] ?? null,
		);
	}

	/**
	 * Returns the first choice (message) if available
	 *
	 * @return array|null
	 */
	public function getFirstChoice(): ?array
	{
		return $this->choices[0] ?? null;
	}

	/**
	 * Returns the content of the first choice if available
	 *
	 * @return string|null
	 */
	public function getFirstContent(): ?string
	{
		if (isset($this->choices[0]['message']['content'])) {
			return $this->choices[0]['message']['content'];
		}
		return null;
	}

	/**
	 * Returns the usage array
	 * @return array
	 */
	public function getUsage(): array
	{
		return $this->usage;
	}
}