<?php

trait CategorySuggestionTrait
{

	/**
	 * @param string $string
	 * @return bool
	 */
	public function matches(string $string): bool
	{
		$needle = mb_strtolower($this->Filter);
		$haystack = mb_strtolower($string);

		$mbpos = mb_strpos($haystack, $needle);

		return $mbpos !== false;
	}

	/**
	 * @return Category
	 */
	public function getCategory(): Category
	{
		return new Category($this->CategoryId);
	}

	/**
	 * @return bool
	 */
	public function shallAutoClose(): bool
	{
		return $this->getAutoCloseAsInt() > 0;
	}

	public function isEnabled()
	{
		return $this->getEnabledAsBool();
	}

	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"category" => $this->getCategory()->toJsonObject(),
			"filter" => $this->Filter,
			"autoclose" => $this->shallAutoClose(),
			"enabled" => $this->isEnabled(),
		];
	}

	public function delete()
	{
		global $d;
		$_q = "DELETE FROM CategorySuggestion WHERE CategorySuggestionId = :id";
		$d->queryPDO($_q, ["id" => $this->getCategorySuggestionIdAsInt()]);
		return true;
	}

}