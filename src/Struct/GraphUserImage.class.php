<?php

namespace Struct;

class GraphUserImage
{
	public string $base64;
	public string $mimeType = 'image/jpeg'; // Default from Graph

	public function __construct(string $binaryData)
	{
		$this->base64 = base64_encode($binaryData);
	}

	/**
	 * Returns the image as data URI, directly browser-compatible.
	 */
	public function asDataUri(): string
	{
		return "data:{$this->mimeType};base64," . $this->base64;
	}

	/**
	 * Optional: Returns pure Base64 string
	 */
	public function getBase64(): string
	{
		return $this->base64;
	}

	public function hasImage(): bool
	{
		return strlen($this->base64) > 0;
	}

}
