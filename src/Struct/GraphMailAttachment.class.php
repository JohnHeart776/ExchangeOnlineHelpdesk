<?php

namespace Struct;


class GraphMailAttachment
{
	public ?string $id = null;
	public string $name = '';
	public string $content_type = '';
	public int $size = 0;
	public bool $is_inline = false;
	public ?string $content = null;

	public static function fromGraphData(array $data): self
	{
		$att = new self();

		$att->id = $data['id'] ?? null;
		$att->name = $data['name'] ?? '';
		$att->content_type = $data['contentType'] ?? '';
		$att->size = $data['size'] ?? 0;
		$att->is_inline = $data['isInline'] ?? false;
		$att->content = isset($data['contentBytes']) ? base64_decode($data['contentBytes']) : null;

		return $att;
	}

	/**
	 * Converts a GraphMailAttachment into a regular MailAttachment
	 */
	public function toMailAttachment(): \MailAttachment
	{
		$att = new \MailAttachment(0);

		$att->AzureId = $this->id;
		$att->Secret1 = hash("sha256", \GuidHelper::generateGuid());
		$att->Secret2 = hash("sha256", \GuidHelper::generateGuid());
		$att->Secret3 = hash("sha256", \GuidHelper::generateGuid());
		$att->Name = $this->name;
		$att->ContentType = $this->content_type;
		$att->Size = $this->size;
		$att->IsInline = $this->is_inline;
		$att->HashSha256 = hash("sha256", $this->content);
		$att->Content = base64_encode($this->content);

		return $att;
	}
}
