<?php

trait TemplateTextTrait
{
	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->getName(),
			"description" => $this->getDescription(),
		];
	}

	public function getAgentLink()
	{
		return "/agent/templatetext/" . $this->getGuid();
	}

	public function delete(): true
	{
		global $d;
		$_q = "DELETE FROM TemplateText WHERE TemplateTextId = :id";
		$d->queryPDO($_q, ["id" => $this->getTemplateTextIdAsInt()]);
		return true;
	}

	/**
	 * @return string
	 */
	public function getContentForTinyMce(): string
	{
		return htmlspecialchars($this->getContent() ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8', false);
	}


}
