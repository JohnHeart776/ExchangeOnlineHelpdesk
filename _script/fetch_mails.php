<?php
require_once __DIR__ . '/../src/bootstrap.php';
global $d;

function out(string $text): void
{
	echo date("Y-m-d H:i:s ") . $text . PHP_EOL;
}

$sourceMailbox = Config::getConfigValueFor("source.mailbox");
$maxMailsToFetchFromGraph = Config::getConfigValueFor("source.mailCount");
//$maxMailsToFetchFromGraph = 100; //DEBUG ONLY

$reset = in_array('--reset', $argv, true);

if (Config::getConfigValueFor("debug.resetBeforeImport") || $reset) {
	out("Resetting Database before Import");
	out("Truncating AiCache...");
	$d->query("TRUNCATE TABLE `AiCache`");
	out("Truncating Ticket...");
	$d->query("TRUNCATE TABLE `Ticket`");
	out("Truncating TicketComment...");
	$d->query("TRUNCATE TABLE `TicketComment`");
	out("Truncating TicketActionItem...");
	$d->query("TRUNCATE TABLE `TicketActionItem`");
	out("Truncating TicketStatus...");
	$d->query("TRUNCATE TABLE `TicketStatus`");
	out("Truncating TicketAssociate...");
	$d->query("TRUNCATE TABLE `TicketAssociate`");
	out("Truncating TicketFile...");
	$d->query("TRUNCATE TABLE `TicketFile`");
	out("Truncating Mail...");
	$d->query("TRUNCATE TABLE `Mail`");
	out("Truncating MailAttachment...");
	$d->query("TRUNCATE TABLE `MailAttachment`");
	out("Truncating MailAttachmentIgnore...");
	$d->query("TRUNCATE TABLE `MailAttachmentIgnore`");
	out("Database reset completed.");
}

try {

	out("== START fetch.php for {$sourceMailbox} ==");

	$graphClient = GraphHelper::getApplicationAuthenticatedGraph();

	$sourceMailbox = Config::getConfigValueFor("source.mailbox");
	$graphMails = $graphClient->fetchMails(
		$sourceMailbox,
		$maxMailsToFetchFromGraph
	);

	out("Found emails: " . count($graphMails));

	foreach (array_reverse($graphMails) as $graphMail) { //reverse array so the oldest mails are created first
		/** @var \Struct\GraphMail $graphMail */

		//if there is no messageId (should never happen except on drafts or manually crafted mails)
		if (empty($graphMail->internetMessageId)) {
			out("️ Mail without internetMessageId, will not be saved.");
			out("   Subject: {$graphMail->subject}");
			out("   From:    {$graphMail->from_name} <{$graphMail->from_email}>");
			continue;
		}

		out("SecureObjectHash: {$graphMail->secureObjectHash}");

		$exists = MailController::exist("SecureObjectHash", $graphMail->secureObjectHash);

		if ($exists) {
			$mail = MailController::searchBy("SecureObjectHash", $graphMail->secureObjectHash, true);
			out("[!] Mail already saved as MailId " . $mail->MailId . PHP_EOL .
				"\t" . $graphMail->id . PHP_EOL .
				"\t{$graphMail->received_datetime}, {$graphMail->from_email}, {$graphMail->subject}" . PHP_EOL
			);
			continue;
		}

		$newMail = $graphMail->toMail();
		$mail = MailController::save($newMail);
		$mail->spawn();

		out("Message-ID: {$mail->MessageId}");
		out("Received: " . $mail->getReceivedDatetimeAsDateTime()->format("c"));
		out("From: {$mail->FromName} <{$mail->FromEmail}>");
		out("Subject: {$mail->Subject}");
		out("To: " . implode(', ', $mail->getToRecipientsAsArray()));

		out("Searching attachments");
		out("Fetching attachments from Azure for mail ID: " . $mail->AzureId);

		$graphMailAttachments = $graphClient->fetchAttachments(
			$sourceMailbox,
			$mail->AzureId
		);

		foreach ($graphMailAttachments as $k => $graphMailAttachment) {
			out("  processing attachment ".$graphMailAttachment->name);
			$attachmentExists = MailAttachmentController::exist("AzureId", $graphMailAttachment->id);
			if (!$attachmentExists) {
				$newMailAttachment = $graphMailAttachment->toMailAttachment();

				if (MailAttachmentIgnore::isMailAttachmentIgnored($newMailAttachment)) {
					out("   ignoring Attachment based on hash ".$newMailAttachment->getHashSha256());
				} else {
					$MailAttachment = MailAttachmentController::save($newMailAttachment);
					$updateResult = $MailAttachment->linkToMail($mail);
					out ("   saved attachment with hash ".$newMailAttachment->getHashSha256());
					out("  → {$MailAttachment->Name} ({$MailAttachment->Size} Bytes)");
				}
			} else {
				$MailAttachment = MailAttachmentController::searchOneBy("AzureId", $graphMailAttachment->id);
				out("  → {$MailAttachment->Name} ({$MailAttachment->Size} Bytes)");
			}

		}

		// Update HasAttachments flag based on actual attachment count
		$mail->update("HasAttachments", count($graphMailAttachments) > 0 ? 1 : 0);

		$desiredTicketStatus = null;
		out("Processing ticket status...");

		if (!$mail->subjectContainsATicketMarker()) {

			$ticket = TicketHelper::createNewTicketFromGraphMail($graphMail, $mail);
			out("\tTicket has been created, Ticket-ID: {$ticket->TicketId}");
			$desiredTicketStatus = TicketStatusHelper::getDefaultStatus();

		} else {

			//this mail subject contains a ticket marker, so we're going to try to find the ticket'
			$ticketNumber = $mail->extractTicketNumberFromSubject();
			out("\t[!] Ticket number found in subject: $ticketNumber");
			$ticket = Ticket::byNumber($ticketNumber);

			if (!$ticket) {

				//the ticket does not exist, so we're going to create a new one, the marker's worthless
				out("\t[!] Ticket with subject number $ticketNumber does not exist, creating new one");
				$ticket = TicketHelper::createNewTicketFromGraphMail($graphMail, $mail);
				$desiredTicketStatus = TicketStatusHelper::getDefaultStatus();

				$extractedBadMarker = TicketHelper::extractTicketMarkerFromString($mail->Subject);
				//remove the wrong subject from the ticket marker
				out("\t* cleanup ticket subject (removing marker {$extractedBadMarker})");

				$newMailSubjectWithoutTicketMarker = TicketHelper::removeTicketMarkerFromString($ticket->Subject);
				$graphClient->updateMailSubject(
					$sourceMailbox,
					$mail->AzureId,
					$newMailSubjectWithoutTicketMarker,
				);
				$ticket->update("Subject", $newMailSubjectWithoutTicketMarker);;
				$ticket->spawn();

			} else {

				out("\tusing found ticket: {$ticket->TicketId} (Status: {$ticket->getStatus()->getPublicName()})");
				if ($ticket->getStatus()->isClosed()) {
					$ticket->setStatus(TicketStatusHelper::getDefaultStatus());
					$ticket->addTicketComment(
						text: "Ticket reopened due to incoming mail",
						facility: EnumTicketCommentFacility::automatic
					);
					out("\tTicket was closed and has been reopened.");
				} else {
					$desiredTicketStatus = TicketStatusHelper::getDefaultCustomerReplyStatus();
					out("\tsetting ticket status to Customer Reply");
				}

				$ticket->addTicketComment(
					"Connecting mail response from mail " . $mail->getGuid() . " with this ticket",
					facility: EnumTicketCommentFacility::automatic,
					accessLevel: EnumTicketCommentAccessLevel::Public,
				);

			}

			if (TicketHelper::stringContainsATicketMarker($ticket->getSubject())) {
				//the ticket subject contains a ticket marker, remove that one
				$marker = TicketHelper::extractTicketMarkerFromString($ticket->getSubject());
				out("\t* cleaning up ticket subject (removing marker $marker)");
				$newTicketSubject = TicketHelper::removeTicketMarkerFromString($ticket->getSubject());
				$ticket->update("Subject", $newTicketSubject);
				$ticket->spawn();
			}
		}

		//Add the initial Comment that holds the mail body
		$newTicketComment = TicketTooling::convertGraphMailToUnsavedTicketComment($graphMail, $ticket);
		$newTicketComment->AccessLevel = 'Public';
		$newTicketComment->IsEditable = 1; //the first comment is editable by agents
		$ticketComment = TicketCommentController::save($newTicketComment);

		out("Determining ticket category suggestion...");
		$suggestedCategory = CategoryHelper::suggestCategory($ticket);
		if ($suggestedCategory) {
			$ticket->setCategory($suggestedCategory->getCategory(), "Ticket category automatically set to '{$suggestedCategory->getCategory()->getPublicName()}'");

			$ticket->addTicketComment(
				text: "Kategorie Match auf Grund von Such-Phrase \"" . $suggestedCategory->Filter . "\"",
				facility: EnumTicketCommentFacility::automatic,
			);

			// Auto-close has a higher priority than other status settings
			if ($suggestedCategory->shallAutoClose()) {
				$desiredTicketStatus = TicketStatusHelper::getDefaultClosedStatus();
				$ticket->addTicketComment(
					text: "Automatically closed due to category match " . $suggestedCategory->Guid,
					facility: EnumTicketCommentFacility::automatic,
				);
			}
		}

		//Set the final Ticket status based on all processing
		if ($desiredTicketStatus) {
			$ticket->setStatus($desiredTicketStatus);
			out("\tFinal ticket status set to: " . $desiredTicketStatus->getPublicName());
		}

		$ticketComment->linkMail($mail);

		//now retrieve the associate from this Mail and Assign it to Ticket
		out("Processing ticket associates...");
		out("Attempting to get sender as organization user...");
		$ouser = $mail->getSenderAsOrganizationUser();
		if ($ouser) {
			$ticketAssociate = $ticket->linkTicketAssociate($ouser);
			out("using e-mail sender as associate: " . $ouser->DisplayName . " is saved as " . $ticketAssociate->getGuid());
		} else {

			//find all mails that are inside the ticket body
			$pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
			preg_match_all($pattern, $ticketComment->getText(), $matches);

			if (!empty($matches[0])) {
				foreach (array_unique($matches[0]) as $email) {

					if ($email == Config::getConfigValueFor("source.mailbox")) {
						out("Skipping Mail-Sender $email because that's our Mailbox");
						continue;
					}


					$emailUser = OrganizationUserController::searchOneBy('Mail', $email);

					if ($emailUser) {
//						$ticket->addTicketComment(
//							text: "DEBUG: found Ouser in search: " . print_r($emailUser, true),
//						);
						$ticketAssociate = $ticket->linkTicketAssociate($emailUser);
						$ticket->addTicketComment(
							text: "Email address {$email} was found as ticket associate",
							facility: EnumTicketCommentFacility::automatic,
						);
						out("found email in ticket body: " . $email . " linked as associate " . $ticketAssociate->getGuid());
					}
				}
			} else {
				out("no email found in ticket body");
//				$ticket->addTicketComment(
//					text: "DEBUG: no email found in ticket body",
//				);
			}

		}

		if (Config::getConfigValueFor("source.mailbox.suffixSubject") && !str_contains($graphMail->subject, $ticket->getTicketMarkerForMailSubject())) {
			//if the Ticket does not Contain our Marker, we're going to add it
			$prefix = $ticket->getTicketMarkerForMailSubject();
			out("suffixing mail subject with $prefix");
			$graphClient->suffixMailSubject(
				$sourceMailbox,
				$mail->AzureId,
				"$prefix"
			);

		}

		out("————————————————————");
	}

	out("");
	out("————————————————————");

	out("== END fetch.php successfully for {$sourceMailbox} ==");

} catch (Exception $e) {

	$error = "[!!] Error: " . $e->getMessage();
	out($error . "\n");
	out($e->getTraceAsString() . "\n");

}
