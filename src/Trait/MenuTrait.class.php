<?php

trait MenuTrait
{

	/**
	 * @return MenuItem[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getItems(): array
	{
		global $d;
		$_q = "SELECT MenuItemId FROM MenuItem
                  WHERE MenuId = :menuId AND ParentMenuItemId IS NULL
                  ORDER BY SortOrder, Title";
		$t = $d->getPDO($_q, ["menuId" => $this->getMenuId()]);
		$r = [];
		foreach ($t as $u) {
			$r[] = new MenuItem((int)$u["MenuItemId"]);
		}
		return $r;
	}

	/**
	 * @return MenuItem[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getMenuItems(): array
	{
		return $this->getItems();
	}

	public function getNextSortOrder(): int
	{
		$max = 0;
		foreach ($this->getItems() as $menuItem) {
			$max = max($max, $menuItem->getSortOrder());
		}
		return $max + 1;
	}

}
