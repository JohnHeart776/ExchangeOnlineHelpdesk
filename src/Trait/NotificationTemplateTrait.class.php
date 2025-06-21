<?php

trait NotificationTemplateTrait
{

	/**
	 * @return array
	 */
	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->getName(),
		];
	}

	public function isEnabled(): bool
	{
		return $this->getEnabledAsInt() > 0;
	}

	/**
	 * @return string
	 */
	public function getMailTextForTinyMce(): string
	{
		return htmlspecialchars($this->getMailText() ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8', false);
	}

	public function renderMailText(
		?User             $user = null,
		?OrganizationUser $organizationUser = null,
		?Ticket           $ticket = null,
	): string
	{

		return MailHelper::renderMailText(
			text: $this->getMailText(),
			user: $user,
			organizationUser: $organizationUser,
			ticket: $ticket,
		);

	}

	public function renderMailSubject(
		?User             $user = null,
		?OrganizationUser $organizationUser = null,
		?Ticket           $ticket = null,
	): string
	{

		return MailHelper::renderMailText(
			text: $this->getMailSubject(),
			user: $user,
			organizationUser: $organizationUser,
			ticket: $ticket,
		);

	}

}