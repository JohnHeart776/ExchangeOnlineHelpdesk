<?php

trait TicketCommentTrait
{
	/**
	 * @return stdClass|null
	 */
	public function getGraphMailAsObject(): ?stdClass
	{
		if (!$this->hasGraphObject())
			return null;
		return json_decode($this->getGraphObject());
	}

	public function hasGraphObject(): bool
	{
		return strlen($this->GetGraphObject()) > 1 && $this->GetGraphObject() !== null;
	}

	/**
	 * @return Mail|null
	 * @throws Exception
	 */
	public function getMailFromGraphObject(): ?Mail
	{
		if (!$this->hasGraphObject())
			return null;
		$MailAzureId = $this->getGraphMailAsObject()->id;
		return MailController::searchBy("AzureId", $MailAzureId, true);
	}

	public function hasMail(): bool
	{
		return $this->getMailIdAsInt() > 0;
	}

	public function getMail(): ?Mail
	{
		if (!$this->hasMail())
			return null;
		return new Mail($this->getMailIdAsInt());
	}

	public function hasUser(): bool
	{
		return $this->getUserIdAsInt() > 0;
	}

	public function getUser(): ?User
	{
		if (!$this->hasUser())
			return null;
		return new User($this->getUserIdAsInt());
	}

	public function getUserIdAsInt(): int
	{
		return $this->getUserId();
	}

	/**
	 * @param Mail $mail
	 * @return $this
	 */
	public function linkMail(Mail $mail): static
	{
		$this->update("MailId", $mail->MailId);
		$this->spawn();
		return $this;
	}

	public function isTextTypeTxt(): bool
	{
		return $this->getTextType() == "txt";
	}

	public function isTextTypeHtml(): bool
	{
		return $this->getTextType() == "html";
	}

	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"created" => $this->getCreatedDatetimeAsDateTime()->format("Y-m-d H:i:s"),
			"text" => $this->getText(),
		];
	}

	public function isOfFacilityUser(): bool
	{
		return $this->getFacility() == "user";
	}

	public function isPublicAccessLevel(): bool
	{
		return $this->getAccessLevel() == EnumTicketCommentAccessLevel::Public->toString();
	}

	/**
	 * @return string
	 */
	public function getTextFormattedForTicket(): string
	{
		if ($this->isTextTypeTxt())
			return nl2br($this->getText());
		return $this->getText();
	}
}