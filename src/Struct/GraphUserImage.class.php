<?php

namespace Struct;

class GraphUserImage
{
	public string $base64;
	public string $mimeType = 'image/jpeg'; // Standard von Graph

	public function __construct(string $binaryData)
	{
		$this->base64 = base64_encode($binaryData);
	}

	/**
	 * Gibt das Bild als data URI zurück, direkt browserfähig.
	 */
	public function asDataUri(): string
	{
		return "data:{$this->mimeType};base64," . $this->base64;
	}

	/**
	 * Optional: Gibt reinen Base64-String zurück
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
