<?php

trait TicketAssociateTrait
{
	use JsonSerializableTrait;
	use EntityRelationshipTrait;
	use MailTemplateTrait;

	/**
	 * @return OrganizationUser
	 */
	public function getOrganizationUser(): OrganizationUser
	{
		return $this->getEntityById('OrganizationUser', 'getOrganizationUserId');
	}

	public function getTicket(): Ticket
	{
		return $this->getEntityById('Ticket', 'getTicketId');
	}

	public function toJsonObject(): array
	{
		return array_merge($this->getBaseJsonFields(), [
			"ticket" => $this->entityToJson($this->getTicket()),
			"organizationuser" => $this->entityToJson($this->getOrganizationUser()),
		]);
	}


	public function sendTicketCommentAsMailMessage(TicketComment $ticketComment): bool
	{
		// Prepare content based on text type
		$content = ($ticketComment->getTextType() != "html") 
			? TextHelper::wrapPlainTextInHtml($ticketComment->getText())
			: $ticketComment->getText();

		// Wrap with mail template and replace placeholders
		$html = $this->wrapWithMailTemplate($content);
		$html = $this->replacePlaceholders($html);

		$subject = $this->getTicket()->getCustomerNotificationMailSubject();
		$to = $this->getOrganizationUser()->getMail();

		if (!$to) {
			throw new \Exception("No mail address found for user " . $this->getOrganizationUser()->getDisplayName());
		}

		return $this->sendStyledMail($to, $subject, $html);
	}

	public function delete(): bool
	{
		global $d;
		$_q = "DELETE FROM TicketAssociate WHERE TicketAssociateId = :id";
		$d->queryPDO($_q, ["id" => $this->getTicketAssociateIdAsInt()]);
		return true;
	}

}
