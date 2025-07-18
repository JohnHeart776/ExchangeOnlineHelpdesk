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

	public function hasUser(): bool
	{
		return $this->getUserIdAsBool();
	}

	public function getCreatedDatetimeAsDateTime(): DateTime
	{
		try {
			return new DateTime($this->getCreatedDatetime());
		} catch (\Exception $e) {
			throw new \Exception("Failed to create DateTime from created datetime: " . $e->getMessage());
		}
	}

	public function isAccessLevelPublic(): bool
	{
		return $this->getAccessLevel() == EnumTicketFileAccessLevel::Public->toString();
	}

}
