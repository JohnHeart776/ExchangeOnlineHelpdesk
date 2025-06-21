<?php

trait TicketTrait
{

	/**
	 * @param Status      $status
	 * @param User|null   $user
	 * @param string|null $additionalTicketCommentText
	 * @param bool|null   $sendAgentNotification
	 * @param User|null   $agentUser
	 * @param bool|null   $sendClientNotification
	 * @return $this
	 * @throws \Database\DatabaseQueryException
	 */
	public function setStatus(Status  $status,
							  ?User   $user = null,
							  ?string $additionalTicketCommentText = null,
							  ?bool   $sendAgentNotification = true,
							  ?User   $agentUser = null,
							  ?bool   $sendClientNotification = true,
	): static
	{
		//persist old status for later
		$oldStatus = $this?->getStatus();

		//update the internal status Id
		$this->update("StatusId", $status->StatusId);

		$body = "Status was changed to " . $status->PublicName . " (from " . ($oldStatus ? $oldStatus->PublicName : "Unknown") . ").";
		if ($additionalTicketCommentText)
			$body .= " " . $additionalTicketCommentText;

		$this->addTicketComment(
			text: $body,
			user: $user,
			accessLevel: EnumTicketCommentAccessLevel::Public
		);

		$this->spawn();

		//add History Record
		$ticketStatus = new TicketStatus(0);
		$ticketStatus->TicketId = $this->getTicketIdAsInt();

		$ticketStatus->OldStatusId = $oldStatus?->StatusId;
		$ticketStatus->OldStatusIdIsFinal = $oldStatus?->getIsFinalAsInt();

		$ticketStatus->NewStatusId = $status->StatusId;
		$ticketStatus->NewStatusIdIsFinal = $status->getIsFinalAsInt();

		$ticketStatus->UserId = $user?->getUserId();
		$newTicketStatus = TicketStatusController::save($ticketStatus);


		//check for triggers
		//agent trigger
		if ($sendAgentNotification && $agentUser) {
			if ($status->hasAgentNotificationTemplate()) {
				//if the current logged in user is not identical to the user which issued the status change
				//e.g. it was done by someone else
				$nt = $status->getAgentNotificationTemplate();
				if ($nt->isEnabled()) {

					$subject = $nt->renderMailSubject(user: $agentUser, ticket: $this);
					$body = $nt->renderMailText(user: $agentUser, ticket: $this);
					//actually send the mail
					$agentUser->sendMailMessage($subject, $body);
				}
			}
		}

		//customer trigger
		if ($sendClientNotification) {

			if ($status->hasCustomerNotificationTemplate()) {
				$nt = $status->getCustomerNotificationTemplate();
				if ($nt->isEnabled()) {

					$associatedUsers = $this->getTicketAssociates();

					foreach ($associatedUsers as $associate) {
						$user = $associate->getOrganizationUser();
						$subject = $nt->renderMailSubject(organizationUser: $user, ticket: $this);
						$body = $nt->renderMailText(organizationUser: $user, ticket: $this);
						//actually send the mail
						$user->sendMailMessage($subject, $body);
					}

				}
			}
		}

		return $this;
	}

	/**
	 * @param string    $text
	 * @param User|null $user
	 * @param string    $facility
	 * @return TicketComment|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function addTicketComment(string                       $text,
									 ?User                        $user = null,
									 EnumTicketCommentFacility    $facility = EnumTicketCommentFacility::system,
									 EnumTicketCommentTextType    $textType = EnumTicketCommentTextType::txt,
									 EnumTicketCommentAccessLevel $accessLevel = EnumTicketCommentAccessLevel::Internal,
	): ?TicketComment
	{
		if (!$textType)
			$textType = "txt";

		$newTicketComment = new TicketComment(0);
		$newTicketComment->TicketId = $this->TicketId; //ensure the ticket comment is linked to that ticket
		$newTicketComment->Facility = $facility->toString();
		$newTicketComment->AccessLevel = $accessLevel->toString();
		$newTicketComment->TextType = $textType->toString();
		$newTicketComment->Text = $text;

		if ($user)
			$newTicketComment->UserId = $user->getUserId();

		$tc = TicketCommentController::save($newTicketComment);

		//update ticket lastUpdate
		$this->update("UpdatedDatetime", date("Y-m-d H:i:s"));

		return $tc;
	}

	/**
	 * @return TicketComment[]
	 * @throws Exception
	 */
	public function getTicketComments(): array
	{
		return TicketCommentController::searchBy("TicketId", $this->TicketId);
	}

	/**
	 * @param Category    $category
	 * @param string|null $commentText
	 * @return Ticket
	 */
	public function setCategory(Category $category, ?string $commentText = null): self
	{
		$this->update("CategoryId", $category->CategoryId);
		if ($commentText) {
			$this->addTicketComment(
				text: $commentText
			);
		}
		return $this->spawn();
	}

	public function getCategory(): Category
	{
		return new Category((int)$this->GetCategoryId());
	}

	public function hasAttachments(): bool
	{
		global $d;
		$_q = "SELECT Count(1) as a
		FROM MailAttachment ma
			LEFT JOIN Mail m ON m.MailId = ma.MailId
			LEFT JOIN Ticket t ON t.ConversationId = m.ConversationId
		WHERE t.TicketId = :ticketId
		ORDER BY m.MailId, ma.MailAttachmentId
		;";
		$t = $d->getPDO($_q, ["ticketId" => $this->getTicketId()], true);
		return ((int)$t["a"]) > 0;

	}

	/**
	 * @return MailAttachment[]
	 * @throws Exception
	 */
	public function getAllAttachments(): array
	{
		$AllMailAttachments = [];

		foreach ($this->getTicketComments() as $ticketComment) {
			$Mail = $ticketComment->getMailFromGraphObject();
			if ($Mail) {
				if ($Mail->hasAttachments()) {
					foreach ($Mail->GetAttachments() as $attachment) {
						$AllMailAttachments[] = $attachment;
					}
				}
			}
		}

		return $AllMailAttachments;
	}

	/**
	 * @return Status|null
	 */
	public function getStatus(): ?Status
	{
		if (!$this->GetStatusId() || $this->GetStatusId() == 0)
			return null;
		return new Status((int)$this->GetStatusId());
	}

	/**
	 * @return Status
	 */
	public function getStatusAsStatus(): Status
	{
		return $this->getStatus();
	}

	/**
	 * @return string
	 */
	public function getTicketMarkerForMailSubject(): string
	{
		return "[[##{$this->TicketNumber}##]]";
	}

	public function isClosed()
	{
		return $this->getStatus()->isClosed();
	}

	public function isDue()
	{
		if ($this->isClosed())
			return false;
		return (new DateTime()) > $this->getDueDatetimeAsDateTime();
	}

	public function getCreatedAsDateEta(): DateEta
	{
		return new DateEta($this->getCreatedDatetimeAsDateTime());
	}

	/**
	 * @return TimeSpan
	 */
	public function getDueETAAsTimespan(): TimeSpan
	{
		return TimeSpan::fromDateTimeObjects(
			new DateTime(),
			$this->getDueDatetimeAsDateTime()
		);
	}

	public function hasMails()
	{
		return $this->getFirstMailForTicket() !== null;
	}

	/**
	 * @return Mail[]|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function getMailsForTicket(): ?array
	{
		return MailController::searchBy("ConversationId", $this->ConversationId);
	}

	/**
	 * @return Mail|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function getFirstMailForTicket(): ?Mail
	{
		return MailController::searchOneBy("ConversationId", $this->ConversationId);
	}

	/**
	 * @return string
	 */
	public function getLink(): string
	{
		return "/ticket/" . $this->TicketNumber;
	}

	/**
	 * @return string
	 */
	public function getAbsoluteLink(): string
	{
		return methods::getAbsoluteDomainLink() . $this->getLink();
	}

	public function getPublicLink()
	{
		return "/ticket/" . $this->TicketNumber . "/" . $this->Secret1;
	}

	/**
	 * @return bool
	 */
	public function hasAssignee(): bool
	{
		return $this->getAssigneeUserIdAsInt() > 0;
	}

	public function getAssigneeAsUser(): ?User
	{
		return UserController::searchOneBy("UserId", $this->getAssigneeUserId());
	}

	public function assignUser(User $user): self
	{
		$this->update("AssigneeUserId", $user->getUserId());
		$this->addTicketComment(
			text: "Assigned to " . $user->getDisplayName() . " (" . $user->getMail() . ")",
			user: login::getUser(),
		);
		$this->spawn();
		return $this;
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->Guid,
			"ticketNumber" => $this->TicketNumber,
			"created" => $this->getCreatedDatetimeAsDateTime()->format("Y-m-d H:i:s"),
			"reportee" => [
				"name" => $this->getMessengerName(),
				"mail" => $this->getMessengerEmail(),
				"image" => $this->getReporteeImage(32),
				"imageLink" => $this->getReporteeAvatarLink(),
				"organizationUser" => $this->messengerIsOrganizationUser() ? $this->getOrganizationUserFromMessenger()->toJsonObject() : null,
			],
			"subject" => $this->Subject,
			"dueDatetime" => $this->getDueDatetimeAsDateTime()->format("Y-m-d H:i:s"),
			"status" => $this->getStatus()->toJsonObject(),
			"statusBadge" => $this->getStatus()->getBadge(),
			"assignee" => $this->getAssigneeAsUser()?->getName(),
			"category" => $this->getCategory()?->toJsonObject(),
			"link" => $this->getLink(),
			"isDue" => $this->isDue(),
			"isOpen" => !$this->isClosed(),
			"isAssigned" => $this->hasAssignee(),
		];
	}

	public function isAssignedToUser(User $user): ?bool
	{
		return $this->getAssigneeAsUser()?->equals($user);
	}

	/**
	 * @return Status[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getAssignableStatus(): array
	{
		global $d;
		$_q = "SELECT StatusId
				FROM Status
				WHERE StatusId NOT IN (" . $this->getStatusId() . ")
				ORDER BY SortOrder;";
		$t = $d->get($_q);
		$r = [];
		foreach ($t as $u) {
			$r[] = new Status((int)$u["StatusId"]);
		}
		return $r;
	}

	/**
	 * @param OrganizationUser $ouser
	 * @return TicketAssociate
	 * @throws \Database\DatabaseQueryException
	 */
	public function linkTicketAssociate(OrganizationUser $ouser): TicketAssociate
	{
		if (!$this->hasTicketAssociateForOrganizationUser($ouser)) {
			$this->addTicketAssociate($ouser);
		}

		return $this->getTicketAssociateRecordForOrganizationUser($ouser);
	}

	/**
	 * @throws \Database\DatabaseQueryException
	 */
	public function hasTicketAssociateForOrganizationUser(OrganizationUser $ouser): bool
	{
		return $this->getTicketAssociateRecordForOrganizationUser($ouser) !== null;
	}

	public function hasTicketAssociates(): bool
	{
		return count($this->getTicketAssociates()) > 0;
	}

	/**
	 * @return TicketAssociate[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketAssociates(): array
	{
		return TicketAssociateController::searchBy("TicketId", $this->getTicketIdAsInt());
	}

	/**
	 * @param OrganizationUser $ouser
	 * @param bool             $sendMail
	 * @return TicketAssociate
	 * @throws \Database\DatabaseQueryException
	 */
	public function addTicketAssociate(OrganizationUser $ouser, bool $sendMail = false): TicketAssociate
	{
		//if this user is already linked, return the existing record
		if ($this->hasTicketAssociateForOrganizationUser($ouser))
			return $this->getTicketAssociateRecordForOrganizationUser($ouser);

		$ticketAssociate = new TicketAssociate(0);
		$ticketAssociate->TicketId = $this->getTicketIdAsInt();
		$ticketAssociate->OrganizationUserId = $ouser->getOrganizationUserId();
		$nta = TicketAssociateController::save($ticketAssociate);

		$this->addTicketComment(
			text: "Associated person added: " . $ouser->getDisplayName(),
			user: login::getUser(),
		);

		if ($sendMail) {
			$nt = NotificationTemplateController::searchOneBy("InternalName", "ta_added");
			if ($nt) {
				$subject = $nt->renderMailSubject(
					organizationUser: $ouser,
					ticket: $this
				);
				$body = $nt->renderMailText(
					organizationUser: $ouser,
					ticket: $this,
				);
				$ouser->sendMailMessage($subject, $body);
			}
		}


		return $nta;
	}

	/**
	 * @param OrganizationUser $ouser
	 * @return TicketAssociate|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketAssociateRecordForOrganizationUser(OrganizationUser $ouser): ?TicketAssociate
	{
		foreach ($this->getTicketAssociates() as $ticketAssociate) {
			if ($ticketAssociate->getOrganizationUser()->equals($ouser))
				return $ticketAssociate;
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getCustomerNotificationMailSubject(): string
	{
		return $this->getSubjectForMailSubject();
	}

	public function getSubjectForMailSubject(bool $prependTicketNumber = true, int $maxChars = 60): string
	{

		//if subject is longer than maxChars chars, cut it
		if (strlen($this->getSubject()) > $maxChars) {
			$sub = substr($this->getSubject(), 0, $maxChars) . "...";
		} else {
			$sub = $this->getSubject();
		}

		if ($prependTicketNumber) {
			$sub = $this->getTicketMarkerForMailSubject() . " " . $sub;
		}

		return $sub;

	}

	/**
	 * @param OrganizationUser $ouser
	 * @return bool
	 * @throws \Database\DatabaseQueryException
	 */
	public function hasAssociatedOrganizationUser(OrganizationUser $ouser): bool
	{
		foreach ($this->getTicketAssociates() as $ticketAssociate) {
			if ($ticketAssociate->getOrganizationUser()->equals($ouser))
				return true;
		}
		return false;
	}

	/**
	 * @param OrganizationUser $ouser
	 * @return TicketAssociate|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketAssociateFromOrganizationUser(OrganizationUser $ouser): ?TicketAssociate
	{
		foreach ($this->getTicketAssociates() as $ticketAssociate) {
			if ($ticketAssociate->getOrganizationUser()->equals($ouser))
				return $ticketAssociate;
		}
		return null;

	}

	public static function getByTicketNumber(string $ticketNumber): ?Ticket
	{
		return TicketController::searchOneBy("TicketNumber", $ticketNumber);
	}

	public static function fromTicketNumber(string $ticketNumber): ?Ticket
	{
		return self::getByTicketNumber($ticketNumber);
	}

	public static function byNumber(string $ticketNumber): ?Ticket
	{
		return self::getByTicketNumber($ticketNumber);
	}

	/**
	 * @return TicketActionItem[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketActionItems(): array
	{
		return TicketActionItemController::searchBy("TicketId", $this->getTicketIdAsInt());
	}

	public function countTicketActionItems(): int
	{
		return count($this->getTicketActionItems());
	}

	public function hasTicketActionItems(): bool
	{
		return $this->countTicketActionItems() > 0;
	}

	/**
	 * @param string      $title
	 * @param string|null $description
	 * @return TicketActionItem|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function addCustomTicketActionItem(string $title, ?string $description = null): ?TicketActionItem
	{
		$ticketActionItem = new TicketActionItem(0);
		$ticketActionItem->TicketId = $this->getTicketIdAsInt();
		$ticketActionItem->Title = $title;
		$ticketActionItem->Description = $description;
		$ticketActionItem->CreatedByUserId = login::getUser()?->getUserId();
		$ticketActionItem->CreatedAt = date("Y-m-d H:i:s");
		return TicketActionItemController::save($ticketActionItem);
	}

	public function addTicketActionItemFromActionItem(ActionItem $actionItem): TicketActionItem
	{
		$newTicketActionItem = new TicketActionItem(0);
		$newTicketActionItem->TicketId = $this->getTicketIdAsInt();
		$newTicketActionItem->Title = $actionItem->getTitle();
		$newTicketActionItem->Description = $actionItem->getDescription();
		$newTicketActionItem->CreatedByUserId = login::getUser()?->getUserId();
		$newTicketActionItem->CreatedAt = date("Y-m-d H:i:s");
		$newTicketActionItem->ActionItemId = $actionItem->getActionItemId();
		return TicketActionItemController::save($newTicketActionItem);
	}

	/**
	 * @return int
	 */
	public function countCompletedTicketActionItems(): int
	{
		return count($this->getCompletedTicketActionItems());
	}

	public function countOpenTicketActionItems(): int
	{
		return $this->countTicketActionItems() - $this->countCompletedTicketActionItems();
	}

	/**
	 * @return TicketActionItem[]
	 */
	public function getCompletedTicketActionItems(): array
	{
		$r = [];
		foreach ($this->getTicketActionItems() as $ticketActionItem) {
			if ($ticketActionItem->isCompleted()) {
				$r[] = $ticketActionItem;
			}
		}
		return $r;
	}

	public function countCompletedTicketActionItemsInPercent(): float
	{
		return $this->countCompletedTicketActionItems() * 100 / $this->countTicketActionItems();
	}

	public function getReporteeAvatarLink(): string
	{
		return "/api/organizationuser/{$this->getMessegerEmailSafe()}/image.jpg";
	}

	/**
	 * Generate HTML for reportee avatar image
	 * @param int|null $width Width in pixels
	 * @return string HTML code for avatar image
	 */
	public function getReporteeImage(?int $width = 64): string
	{
		$style = "border-radius: 50%; width: {$width}px;";
		return "<img src='" . $this->getReporteeAvatarLink() . "' style='$style' title='{$this->getMessengerName()}' />";
	}

	public function getMessengerNameSafe(): string
	{
		$name = $this->getMessengerName();
		if (!$name)
			$name = $this->getMessengerEmail();
		if (!$name)
			$name = "Unkown";
		return $name;
	}

	public function getMessegerEmailSafe(): string
	{
		$email = $this->getMessengerEmail();
		if (!$email)
			$email = "unkown@mail.com";
		return $email;
	}

	public function getReporteeName(int $maxLength = 50): string
	{
		$name = $this->getMessengerNameSafe();
		if (strlen($name) > $maxLength) {
			return substr($name, 0, $maxLength - 3) . '...';
		}
		return $name;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function extractAllText(): string
	{
		$text = "--- BEGIN TICKET " . $this->getTicketId() . " ---" . PHP_EOL;
		$text .= "Ticket-From: " . $this->MessengerEmail . " " . PHP_EOL .
			"Ticket-From-Name: " . $this->MessengerName . PHP_EOL .
			"Ticket-Title: " . $this->Subject . PHP_EOL . PHP_EOL;

		foreach ($this->getTicketComments() as $k => $tc) {
			$mail = $tc->getMail();

			$text .= PHP_EOL . " --- BEGIN TICKET MESSAGE #" . ($k + 1) . " --- " . PHP_EOL;
			if ($k == 0)
				$text .= "This is the first initial Message of the Ticket. This one represents the original Question of the User and hold the reportee information. All other Comments are technicals or Follow Ups but his one's the first message for this Thread. There might be other replies by agents or the client afterwards" . PHP_EOL;

			$text .= "Received: " . $tc->getCreatedDatetimeAsDateTime()->format("Y-m-d H:i:s") . PHP_EOL;
			$text .= "Comment-Type: " . $tc->getFacility() . PHP_EOL;
			if ($tc->hasUser()) {
				$text .= "Created by User: " . $tc->getUser()?->getName() . PHP_EOL;
			}

			if ($mail) {
				$text .= PHP_EOL;
				$text .= "This Part of the ticket is based on a ** Mail **" . PHP_EOL;
				$text .= "This Mail was received on \"" . $mail->getReceivedDatetimeAsDateTime()->format("Y-m-d H:i:s") . "\"" . PHP_EOL;
				$text .= "This Mail was sent from \"" . $mail->getSenderEmail() . "\"" . PHP_EOL;
				$text .= "This Mails Sender Name was \"" . $mail->getSenderName() . "\"" . PHP_EOL;
			}

			if ($k === 0) {
			}
			$text .= "Content-Type: " . $tc->getTextType() . PHP_EOL;
			$text .= PHP_EOL;
//			$text .= str_replace(["\r\n", "\r", "\n"], '', $tc->getText());
//			$text .= str_replace(["\t"], '', $tc->getText());
			$text .= "*** Human Content Start ***" . PHP_EOL;
			$text .= PHP_EOL;
			$text .= $tc->getText() . PHP_EOL;

			if ($mail?->hasAttachments()) {
				$text .= PHP_EOL;
				$text .= "This Mail has the following Attachments:" . PHP_EOL;
				foreach ($mail->getAttachments() as $kk => $attachment) {
					if ($attachment->isIgnored()) {
						$text .= " --- BEGIN ATTACHMENT #$k-$kk ---" . PHP_EOL;
						$text .= " THIS ATTACHMENT IS IGNORED AND SHALL BE IGNORED - IT IS MOST LIKELY PART OF THE USER'S MAIL SIGNATURE" . PHP_EOL;
						$text .= " --- END ATTACHMENT #$k-$kk ---" . PHP_EOL;
						continue;
					}
					if ($attachment->isOfTypeText()) {
						$text .= " --- BEGIN ATTACHMENT #$k-$kk --- " . PHP_EOL;
						$text .= "Attachment Name: " . $attachment->getName() . PHP_EOL;
						$text .= "*** Attachment Content Start *** " . PHP_EOL;
						$text .= $attachment->getContentDecoded() . PHP_EOL;
						$text .= "*** Attachment Content End *** " . PHP_EOL;
						$text .= " --- END ATTACHMENT #$k-$kk --- " . PHP_EOL;
					} else if ($attachment->hasTextRepresentation()) {
						$text .= " --- BEGIN ATTACHMENT #$k-$kk --- " . PHP_EOL;
						$text .= "Attachment Name: " . $attachment->getName() . PHP_EOL;
						$text .= "*** ATTENTION: This is a transcribed version of the file that was generated using computer vision. *** " . PHP_EOL;
						$text .= "*** A transcribed version of the file follows *** " . PHP_EOL;
						$text .= "*** BEGIN ***" . PHP_EOL;
						$text .= $attachment->generateTextRepresentation();
						$text .= "*** END *** " . PHP_EOL;
						$text .= " --- END ATTACHMENT #$k-$kk --- " . PHP_EOL;
					}
				}
			}
			$text .= PHP_EOL;
			$text .= "*** Human Content End ***" . PHP_EOL;
			$text .= PHP_EOL;

			$text .= PHP_EOL . " --- END  TICKET MESSAGE #" . ($k + 1) . " --- " . PHP_EOL;
		}
		return $text;
	}

	/**
	 * @param ActionGroup $actionGroup
	 * @return TicketActionItem[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function addActionGroup(ActionGroup $actionGroup): array
	{
		$r = [];
		foreach ($actionGroup->getActionItems() as $actionItem) {
			$addedItem = $this->addTicketActionItemFromActionItem($actionItem);
			$r[] = $addedItem;
		}
		return $r;
	}

	public function messengerIsOrganizationUser(): bool
	{
		return $this->getOrganizationUserFromMessenger() != null;
	}

	public function hasValidMessenger(): bool
	{
		return $this->getMessengerEmail() != "";
	}

	public function getOrganizationUserFromMessenger(): ?OrganizationUser
	{
		if (!$this->hasValidMessenger()) {
			return null;
		}
		return OrganizationUserController::searchOneBy("Mail", $this->getMessengerEmail());
	}

	public function getDueDatetimeAsDateEta(): DateEta
	{
		return new DateEta($this->getDueDatetimeAsDateTime());
	}


	public function isOpen()
	{
		return $this->getStatus()->isOpen();
	}

	public function close(?User $user = null)
	{
		$closedStatus = TicketStatusHelper::getDefaultClosedStatus();
		$this->setStatus($closedStatus);
		$this->addTicketComment(
			text: "The ticket has been closed",
			user: $user,
			accessLevel: EnumTicketCommentAccessLevel::Public);
		$this->spawn();
	}

	/**
	 * @return string|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function summarizeWithAi(): ?string
	{
		$text = $this->extractAllText();

		$openAi = new OpenAiClient(
			authenticator: OpenAiApiAuthenticator::getDefault(),
			appendBaselinePromptToPrompt: false,
		);

		$prompt = Config::getConfigValueFor("ai.prompt.summary.ticket") . " \r\n\r\n" . $text;
		return AiService::getRepsonse($prompt);
	}

	public function getValueForEditable(string $key)
	{
		//encode special html values of that key inside the object and return it safe
		$value = $this->$key;
		$value = htmlentities($value);
		return $value;
	}


	public function hasTicketFiles(): bool
	{
		global $d;
		$_q = "SELECT count(1) as a from TicketFile WHERE ticketId = {$this->getTicketIdAsInt()}";
		$t = $d->get($_q);
		return $t[0]["a"] > 0;
	}

	/**
	 * @return TicketFile[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketFiles(): array
	{
		return TicketFileController::searchBy("TicketId", $this->getTicketIdAsInt());
	}

	public function countTicketFiles(): int
	{
		return count($this->getTicketFiles());
	}

	public function copy(): Ticket
	{

		$newTicketNumber = TicketNumberHelper::getNextTicketNumber();

		//copy this ticket
		$newTicketEnvelope = clone $this;
		$newTicketEnvelope->TicketId = null;
		$newTicketEnvelope->Guid = null;
		$newTicketEnvelope->TicketNumber = $newTicketNumber;
		$newTicketEnvelope->Secret1 = TicketTooling::getTicketSecret(12);
		$newTicketEnvelope->Secret2 = TicketTooling::getTicketSecret(16);
		$newTicketEnvelope->Secret3 = TicketTooling::getTicketSecret(24);
		$newTicketEnvelope->StatusId = TicketStatusHelper::getDefaultStatusId();
		$newTicketEnvelope->Subject = "[Kopie] " . $this->getSubject();
		$newTicketEnvelope->CreatedDatetime = date("Y-m-d H:i:s");
		$newTicket = TicketController::save($newTicketEnvelope);

		foreach ($this->getTicketComments() as $ticketComment) {
			$newTicketCommentEvelope = clone $ticketComment;
			$newTicketCommentEvelope->TicketCommentId = null;
			$newTicketCommentEvelope->Guid = null;
			$newTicketCommentEvelope->TicketId = $newTicket->getTicketIdAsInt();
			TicketCommentController::save($newTicketCommentEvelope);
		}

		foreach ($this->getTicketActionItems() as $ticketActionItem) {
			$newTicketActionItemEvelope = clone $ticketActionItem;
			$newTicketActionItemEvelope->TicketActionItemId = null;
			$newTicketActionItemEvelope->Guid = null;
			$newTicketActionItemEvelope->TicketId = $newTicket->getTicketIdAsInt();
			TicketActionItemController::save($newTicketActionItemEvelope);
		}

		foreach ($this->getTicketFiles() as $ticketFile) {
			$newTicketFileEvelope = clone $ticketFile;
			$newTicketFileEvelope->TicketFileId = null;
			$newTicketFileEvelope->Guid = null;
			$newTicketFileEvelope->TicketId = $newTicket->getTicketIdAsInt();
			TicketFileController::save($newTicketFileEvelope);
		}

		foreach ($this->getTicketAssociates() as $ticketAssociate) {
			$newTicketAssociateEvelope = clone $ticketAssociate;
			$newTicketAssociateEvelope->TicketAssociateId = null;
			$newTicketAssociateEvelope->Guid = null;
			$newTicketAssociateEvelope->TicketId = $newTicket->getTicketIdAsInt();
			TicketAssociateController::save($newTicketAssociateEvelope);
		}

		//add ticket Comment to old Ticket
		$this->addTicketComment(
			text: "This ticket has been copied to the new ticket {$newTicketNumber}. <a href=\"" . $newTicket->getLink() . "\">Link to new ticket {$newTicketNumber}</a>",
			user: login::getUser(),
			facility: EnumTicketCommentFacility::other,
		);

		$newTicket->addTicketComment(
			text: "This ticket was copied from ticket {$this->TicketNumber}. <a href=\"" . $this->getLink() . "\">Link to original ticket {$this->TicketNumber}</a>",
			user: login::getUser(),
			facility: EnumTicketCommentFacility::other,
		);

		return $newTicket;

	}

	public function determineDueDateWithAi(): DateTime
	{
		$response = AiService::getRepsonse(
			"You will be given the following ticket: " . PHP_EOL .
			$this->extractAllText() . PHP_EOL .
			PHP_EOL .
			"Determine the most likely due date for this ticket. Search the text and find the correct date. " . PHP_EOL .
			"If you dont find any plausible Due-Date use " . date("Y-m-d", strtotime("+2 days")) . PHP_EOL .
			"Return the date as serialized Json like this {\"year\": (int), \"month\": (int), \"day\": ...}"
		);
		$data = json_decode($response);
		if (!$data)
			throw new Exception("Invalid JSON Response");
		$dt = new DateTime((int)$data->year . "-" . (int)$data->month . "-" . (int)$data->day);
		$dt->setTime(12, 0);
		return $dt;
	}

	public function setDueDate(DateTime $dueDate): true
	{
		$dueDateTimeStr = $dueDate->format("Y-m-d H:i:s");
		$this->update("DueDatetime", $dueDateTimeStr);
		return true;
	}

	public function getRecapWithAi(): string
	{
		$prompt =
			"You will be given the following ticket: " . PHP_EOL .
			$this->extractAllText() . PHP_EOL .
			PHP_EOL .
			"Create a chronological recap of the ticket: What was requested? Extract the tasks that were requested, extract what was done." . PHP_EOL .
			"This Recap shall be in a list style, dont use too long sentenced but keep them simple, if there are multiple requests in one sentence split it into seperate lines" . PHP_EOL .
			"Display in a chronological manner, output everything in german language and format text in html. " . PHP_EOL .
			"In the Ticket Text itself look what was dont by other agents. " . PHP_EOL .
			"Display a Header like 'Ticket Recap as of date time (but in german) " . PHP_EOL .
			"In the End create a Summary of what tasks are open " . PHP_EOL .
			"Only use content that comes from a human, omit technical stuff like status changes and assignee chanes and so on " . PHP_EOL .
			"Since you are generating only display the content INSIDE the Body Node of the HTML. " . PHP_EOL .
			"Feel free to use formatting like bold italic and so on as well to better style your text output and improve reability. " . PHP_EOL .
			"If there are due dates you can also use yellow text-marker style formatting (bold and yellow bg color of span)" . PHP_EOL .
			"When recapping dont use a too generic approach in the beginning of the list, jsut start with the facts, less is more, be precise!" . PHP_EOL .
			"";
		$response = AiService::getRepsonse($prompt);
		return $response;
	}

}