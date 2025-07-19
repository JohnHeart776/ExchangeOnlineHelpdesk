<?php

trait MailTrait
{
	/**
	 * @return \Struct\MailRecipient[]
	 */
	public function getToRecipientsAsArray(): array
	{
		return $this->parseRecipients($this->ToRecipients);
	}

	/**
	 * @return \Struct\MailRecipient[]
	 */
	public function getCcRecipientsAsArray(): array
	{
		return $this->parseRecipients($this->CcRecipients);
	}

	/**
	 * @return \Struct\MailRecipient[]
	 */
	public function getBccRecipientsAsArray(): array
	{
		return $this->parseRecipients($this->BccRecipients);
	}

	public function getHeadersAsArray(): array
	{
		// If HeadersJson is empty, return an empty array.
		if (empty($this->HeadersJson)) {
			return [];
		}
		return explode(", ", $this->HeadersJson);
	}

	/**
	 * @param string $recipientsJson
	 * @return \Struct\MailRecipient[]
	 */
	protected function parseRecipients(string $recipientsJson): array
	{
		$a = json_decode($recipientsJson);
		if (empty($a))
			return [];
		$r = [];
		foreach ($a as $b)
			$r[] = new \Struct\MailRecipient($b->emailAddress->name, $b->emailAddress->address);
		return $r;
	}

	public function hasAttachments(): bool
	{
		return (bool)$this->GetHasAttachments();
	}

	/**
	 * @return int
	 * @throws \Database\DatabaseQueryException
	 */
	public function countAttachments(): int
	{
		global $d;
		$_q = "SELECT count(1) as a FROM MailAttachment WHERE MailId = :mailId";
		$t = $d->getPDO($_q, ["mailId" => $this->MailId], true);
		return (int)$t["a"];
	}

	/**
	 * @return MailAttachment[]|null
	 * @throws Exception
	 */
	public function getAttachments(): ?array
	{
		return MailAttachmentController::searchBy("MailId", $this->MailId);
	}

	public function getActualMailFromGraph()
	{
		$client = GraphHelper::getApplicationAuthenticatedGraph();
		return $client->getMailFromAzureAsGraphMail($this->SourceMailbox, $this->AzureId);
	}

	/**
	 * @return ?OrganizationUser
	 * @throws \Database\DatabaseQueryException
	 */
	public function getSenderAsOrganizationUser(): ?OrganizationUser
	{
		return OrganizationUserController::searchOneBy("UserPrincipalName", $this->getSenderEmail());
	}

	public function exportAsJson(): array
	{
		return [
			"messageId" => $this->MessageId,
			"id" => $this->getAzureId(),
			"received" => $this->getReceivedDatetimeAsDateTime()->format("Y-m-d H:i:s"),
			"subject" => $this->Subject,
			"sender" => ["mail" => $this->getSenderEmail(), "name" => $this->getSenderName()],
			"body" => $this->getBodyWithInlineAttachments(),
			"attachments" => array_map(fn($attachment) => $attachment->toJsonObject(), $this->getAttachments() ?? []),
		];
	}

	public function getBodyWithInlineAttachments(): string
	{
		$body = $this->getBody();

		$pattern = '/<img[^>]+src="cid:([^"]+)"[^>]*>/';
		preg_match_all($pattern, $body, $m);
		if (!empty($m)) {
			$attachments = $this->getAttachments();

			foreach ($m[1] as $cid) {
				$attachmentName = explode("@", $cid)[0];

				$attachment = null;
				foreach ($attachments as $attachment) {
					if ($attachment->getName() == $attachmentName && $attachment->getIsInline() == 1) {
						break;
					}
				}

				if ($attachment) {
					$body = str_replace("cid:$cid", "data:" . $attachment->getContentType() . ";base64," . $attachment->getContent(), $body);
				}

			}
		}

		return $body;
	}

	/**
	 * @return Ticket
	 */
	public function getTicket(): Ticket
	{
		return new Ticket($this->getTicketIdAsInt());
	}

	public function suggestSubjectWithAi(): ?string
	{
		$text = $this->getBody();
		$openAi = new OpenAiClient(
			authenticator: OpenAiApiAuthenticator::getDefault(),
			appendBaselinePromptToPrompt: true,
		);
		$prompt = Config::getConfigValueFor("ai.prompt.mail.summary.subject") . " \r\n\r\n" . $text;
		return AiService::getRepsonse($prompt);
	}

	public function subjectContainsATicketMarker(): bool
	{
		//look if there is a marker like [[##202500043F##]] in the subject, if there is return true
		return TicketHelper::stringContainsATicketMarker($this->getSubject());
	}

	public function extractTicketNumberFromSubject(): ?string
	{
		//if the subject contains a marker like [[##202500043F##]] return the number
		if ($this->subjectContainsATicketMarker()) {
			preg_match("/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/", $this->Subject, $matches);
			return $matches[1];
		}
		return null;
	}


}