<?php

trait TicketActionItemTrait
{

	public function getTicket(): Ticket
	{
		return new Ticket($this->getTicketIdAsInt());
	}

	public function getActionItem(): ?ActionItem
	{
		if (!$this->hasActionItem())
			return null;
		return new ActionItem($this->getActionItemIdAsInt());
	}

	public function hasActionItem(): bool
	{
		return $this->getActionItemIdAsInt() > 0;
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"ticket" => $this->getTicket()->toJsonObject(),
			"title" => $this->getTitle(),
			"description" => $this->getDescription(),
			"created" => $this->getCreatedAtAsDateTime()->format("Y-m-d H:i:s"),
		];
	}

	public function isCompleted(): bool
	{
		return $this->getCompletedAsInt() > 0;
	}

	public function toggleCompleted(): void
	{
		if ($this->isCompleted()) {
			$this->setUncompleted();
		} else {
			$this->setCompleted();
		}

		$this->spawn();
	}

	public function setUncompleted(): void
	{
		$this->setNull("Completed");
		$this->setNull("CompletedAt");
		$this->setNull("CompletedByUserId");
		$this->spawn();
	}

	public function setCompleted(): void
	{
		$this->update("Completed", 1);
		$this->update("CompletedAt", date("Y-m-d H:i:s"));
		if (login::isLoggedIn())
			$this->update("CompletedByUserId", login::getUser()->getUserIdAsInt());
		else
			$this->setNull("CompletedByUserId");
		$this->spawn();
	}

	public function getCompletedByAsUser(): ?User
	{
		if ($this->getCompletedByUserIdAsInt() < 1)
			return null;
		return new User($this->getCompletedByUserIdAsInt());
	}

	public function delete(): void
	{
		global $d;
		$_q = "DELETE FROM TicketActionItem WHERE TicketActionItemId = :id";
		$d->queryPDO($_q, ["id" => $this->getTicketActionItemIdAsInt()]);
	}

}
