<?php

trait TicketAssociateTrait
{

	/**
	 * @return OrganizationUser
	 */
	public function getOrganizationUser(): OrganizationUser
	{
		return new OrganizationUser($this->getOrganizationUserIdAsInt());
	}

	public function getTicket(): Ticket
	{
		return new Ticket($this->getTicketIdAsInt());
	}


	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"ticket" => $this->getTicket()->toJsonObject(),
			"organizationuser" => $this->getOrganizationUser()->toJsonObject(),
		];
	}


	public function sendTicketCommentAsMailMessage(TicketComment $ticketComment): bool
	{
		$html = self::getMailTemplateStart();
		if ($ticketComment->getTextType() != "html") {
			$html .= TextHelper::wrapPlainTextInHtml($ticketComment->getText());
		} else {
			$html .= $ticketComment->getText();
		}
		$html .= self::getMailTemplateEnd();

		//replace dynamic placeholders
		$html = self::replacePlaceholders($html);

		$subject = $this->getTicket()->getCustomerNotificationMailSubject();

		$to = $this->getOrganizationUser()->getMail();
		if (!$to)
			throw new \Exception("No mail address found for user " . $this->getOrganizationUser()->getDisplayName());

		return MailHelper::sendStyledMailFromSystemAccount(
			null,
			$to,
			$subject,
			$html
		);


	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function replacePlaceholders(string $text): string
	{
		//locally cache objects one might need
		$ticket = $this->getTicket();
		$user = $this->getOrganizationUser();

		$text = MailHelper::renderMailText(
			text: $text,
			organizationUser: $user,
			ticket: $ticket
		);

		return $text;
	}

	public function getMailTemplateStart()
	{
		return config::getConfigValueFor("mail.template.start");
	}

	public function getMailTemplateEnd()
	{
		return config::getConfigValueFor("mail.template.end");
	}

	public function delete()
	{
		global $d;
		$_q = "DELETE FROM TicketAssociate WHERE TicketAssociateId = :id";
		$d->queryPDO($_q, ["id" => $this->getTicketAssociateIdAsInt()]);
		return true;
	}

}
