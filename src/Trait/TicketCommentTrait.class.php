<?php

trait TicketCommentTrait
{
	use JsonSerializableTrait;
	use EntityRelationshipTrait;
	use BooleanCheckTrait;

	/**
	 * @return stdClass|null
	 */
	public function getGraphMailAsObject(): ?stdClass
	{
		if (!$this->hasGraphObject())
			return null;

		$decoded = json_decode($this->getGraphObject());
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception("Failed to decode graph object JSON: " . json_last_error_msg());
		}

		return $decoded;
	}

	public function hasGraphObject(): bool
	{
		return strlen($this->getGraphObject()) > 1 && $this->getGraphObject() !== null;
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
		return $this->hasEntityById('getMailId');
	}

	public function getMail(): ?Mail
	{
		return $this->getEntityById('Mail', 'getMailId');
	}

	public function hasUser(): bool
	{
		return $this->hasEntityById('getUserId');
	}

	public function getUser(): ?User
	{
		return $this->getEntityById('User', 'getUserId');
	}

	public function getUserIdAsInt(): int
	{
		return $this->getIdAsInt('getUserId');
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
		return $this->isTextType("txt");
	}

	public function isTextTypeHtml(): bool
	{
		return $this->isTextType("html");
	}

	public function toJsonObject(): array
	{
		return array_merge($this->getBaseJsonFields(), [
			"text" => $this->getText(),
		]);
	}

	public function isOfFacilityUser(): bool
	{
		return $this->isFieldEqualTo('getFacility', "user");
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
