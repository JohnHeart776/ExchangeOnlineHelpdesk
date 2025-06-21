<?php

trait TicketFileTrait
{

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
		];
	}

	public function getFile(): File
	{
		return new File($this->getFileIdAsInt());
	}

	public function getFileLink(): string
	{
		return $this->getFile()->getLink();
	}

	public function getTicket(): Ticket
	{
		return new Ticket($this->getTicketIdAsInt());
	}

	public function getUser(): ?User
	{
		if (!$this->hasUser())
			return null;

		return new User($this->getUserIdAsInt());
	}

	public function hasUser()
	{
		return $this->getUserIdAsBool();
	}

	public function getCreatedDatetimeAsDateTime(): DateTime
	{
		return new DateTime($this->getCreatedDatetime());
	}

	public function isAccessLevelPublic(): bool
	{
		return $this->getAccessLevel() == EnumTicketFileAccessLevel::Public->toString();
	}

}
