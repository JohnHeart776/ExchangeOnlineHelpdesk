<?php

trait ActionItemTrait
{
	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"title" => $this->getTitle(),
			"description" => $this->getDescription(),
		];
	}

	/**
	 * @return true
	 * @throws \Database\DatabaseQueryException
	 */
	public function delete(): true
	{
		global $d;
		$_q = "DELETE FROM ActionItem WHERE ActionItemId = :id";
		$d->queryPDO($_q, ["id" => $this->getActionItemIdAsInt()]);
		return true;
	}
}