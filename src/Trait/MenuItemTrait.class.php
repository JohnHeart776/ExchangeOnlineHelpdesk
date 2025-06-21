<?php


trait MenuItemTrait
{

	/**
	 * @return bool
	 * @throws \Database\DatabaseQueryException
	 */
	public function hasEnabledChildren(): bool
	{
		global $d;
		$_q = "SELECT count(1) as a 
				FROM MenuItem 
				WHERE ParentMenuItemId = :menuItemId AND Enabled = 1;";
		$t = $d->getPDO($_q, ["menuItemId" => $this->getMenuItemId()], true);
		return ((int)$t["a"]) > 0;
	}

	/**
	 * @return bool
	 * @throws \Database\DatabaseQueryException
	 */
	public function hasChildren(): bool
	{
		global $d;
		$_q = "SELECT count(1) as a 
				FROM MenuItem 
				WHERE ParentMenuItemId = :menuItemId;";
		$t = $d->getPDO($_q, ["menuItemId" => $this->getMenuItemId()], true);
		return ((int)$t["a"]) > 0;
	}

	/**
	 * @return array
	 * @throws \Database\DatabaseQueryException
	 */
	public function getChildren(): array
	{
		global $d;
		$r = [];
		$_q = "SELECT MenuItemId FROM MenuItem 
				WHERE ParentMenuItemId = :menuItemId
				ORDER BY SortOrder, Title";
		$t = $d->getPDO($_q, ["menuItemId" => $this->getMenuItemId()]);
		foreach ($t as $u) {
			$r[] = new MenuItem((int)$u["MenuItemId"]);
		}
		return $r;
	}


	public function canSee(?User $user = null): bool
	{
		if (!$user)
			return false;

		if ($this->getRequireIsAdminAsBool()) {
			return $user->isAdmin();
		}

		if ($this->getRequireIsAgentAsBool()) {
			return $user->isAgent();
		}

		if ($this->getRequireIsUserAsBool()) {
			return $user->isUser();
		}

		return true;
	}

	public function amIAChild(): bool
	{
		return $this->getParentMenuItemIdAsInt() > 0;
	}

	public function amITheParent(): bool
	{
		return $this->getParentMenuItemIdAsInt() == 0;
	}

	/**
	 * @return MenuItem
	 */
	public function getParent(): MenuItem
	{
		return new MenuItem($this->getParentMenuItemIdAsInt());
	}

	public function getTrimmedLink(): string
	{
		return trim($this->getLink(), "/");
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		$currentServerUrl = trim(explode('?', $_SERVER["REQUEST_URI"])[0], "/");
		if ($currentServerUrl == $this->getTrimmedLink())
			return true; //if I am directly navigated to

		if ($this->amITheParent()) {
			//if I am active, so should my parent
			foreach ($this->getChildren() as $child) {
				if ($child->isActive())
					return true;
			}
		}

		return false;
	}

	public function hasIcon()
	{
		return strlen($this->getIcon()) > 3;
	}

	public function hasImage()
	{
		return $this->getImageFileIdAsInt() > 0;
	}

	/**
	 * @return ?File
	 */
	public function getImageAsFile(): ?File
	{
		if (!$this->hasImage())
			return null;
		return new File($this->getImageFileIdAsInt());
	}

	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"title" => $this->getTitle(),
			"link" => $this->getLink(),
			"icon" => $this->getIcon(),
			"image" => $this->getImageAsFile()?->toJsonObject(),
			"enabled" => $this->getEnabledAsBool(),
			"sortOrder" => $this->getSortOrder(),
		];
	}

	public function delete(): bool
	{
		if ($this->hasChildren()) //only allow deletion after all children have been removed
			return false;
		global $d;
		$_q = "DELETE FROM MenuItem WHERE MenuItemId = :id";
		$d->queryPDO($_q, ["id" => $this->getMenuItemIdAsInt()]);
		return true;
	}

	public function isEnabled(): bool
	{
		return $this->getEnabledAsBool();
	}

}