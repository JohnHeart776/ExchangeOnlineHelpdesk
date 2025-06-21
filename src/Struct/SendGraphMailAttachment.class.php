<?php

namespace Struct;

class SendGraphMailAttachmentStruct
{

	public function __construct(
		public string $name,
		public string $contentType,
		public string $contentBytes,
		public bool   $isInline = false,
		public string $contentId,
	)
	{
		// ...
	}

	public function toArray(): array
	{
		return [
			'name' => $this->name,
			'contentType' => $this->contentType,
			'contentBytes' => $this->contentBytes,
			'isInline' => $this->isInline,
			'contentId' => $this->contentId,
		];
	}
}