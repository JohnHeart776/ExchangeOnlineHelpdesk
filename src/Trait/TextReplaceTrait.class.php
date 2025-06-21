<?php

trait TextReplaceTrait
{

	/**
	 * @param $text
	 * @return string
	 */
	public function replaceIn($text): string
	{
		$search = str_replace(["\r\n", "\r"], "\n", $this->SearchFor);
		$replace = str_replace(["\r\n", "\r"], "\n", $this->ReplaceBy);
		return str_ireplace($search, $replace, $text);
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"searchFor" => $this->getSearchFor(),
			"replaceBy" => $this->getReplaceBy(),
		];
	}

	public function delete()
	{
		global $d;
		$_q = "DELETE FROM TextReplace WHERE TextReplaceId = :id";
		$d->queryPDO($_q, ["id" => $this->getTextReplaceIdAsInt()]);
		return true;
	}

	public function isEnabled(): bool
	{
		return $this->getEnabledAsBool();
	}

}