<?php

trait ActionGroupTrait
{
	/**
	 * @return ActionItem[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getActionItems(): array
	{
		global $d;
		$_q = "SELECT ActionItemId FROM ActionItem WHERE ActionGroupId = :actionGroupId ORDER BY SortOrder";
		$t = $d->getPDO($_q, ["actionGroupId" => $this->getActionGroupId()]);
		$r = [];
		foreach ($t as $u) {
			$r[] = new ActionItem((int)$u["ActionItemId"]);
		}
		return $r;
	}

	/**
	 * @return int
	 * @throws \Database\DatabaseQueryException
	 */
	public function getNextSortOrder(): int
	{
		$max = 0;
		foreach ($this->getActionItems() as $actionItem) {
			$max = max($max, $actionItem->getSortOrder());
		}
		return $max + 1;
	}

	/**
	 * @return array{guid: null|string, name: null|string, sortOrder: int|null, actionItems: array}
	 * @throws \Database\DatabaseQueryException
	 */
	public function toJsonObject(): array
	{
		$r = [];
		foreach ($this->getActionItems() as $actionItem) {
			$r[] = $actionItem->toJsonObject();
		}

		return [
			"guid" => $this->getGuid(),
			"name" => $this->getName(),
			"sortOrder" => $this->getSortOrder(),
			"actionItems" => $r,
		];
	}

	public static function getGlobalNextSortOrder(): int
	{
		global $d;
		$_q = "SELECT MAX(SortOrder) as a FROM ActionGroup";
		$t = $d->getPDO($_q);
		return (int)$t["a"] + 1;
	}
}