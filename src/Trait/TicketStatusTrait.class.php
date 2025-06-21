<?php

trait TicketStatusTrait
{

	/**
	 * @return User|null
	 */
	public function getUser(): ?User
	{
		if (!$this->hasUser())
			return null;
		return new User($this->getUserIdAsInt());
	}

	public function hasUser(): bool
	{
		return $this->GetUserId() > 0;
	}

}