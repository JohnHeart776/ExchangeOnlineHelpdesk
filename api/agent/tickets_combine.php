<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

if (!isset($_POST['tickets']) || !is_array($_POST['tickets'])) {
	die(jsonStatus(false, "No tickets provided for merging."));
}

/** @var Ticket[] $tickets */
$tickets = [];
foreach ($_POST['tickets'] as $ticketId) {
	$ticket = new Ticket($ticketId);
	if (!$ticket->isValid()) {
		die(jsonStatus(false, "Invalid ticket ID: " . $ticketId));
	}
	$tickets[] = $ticket;
}

if (count($tickets) < 2) {
	die(jsonStatus(false, "At least two tickets are required for merging."));
}

$primaryTicket = null;
$oldestDate = null;
$primaryKey = null;

foreach ($tickets as $key => $ticket) {
	if ($oldestDate === null || $ticket->getCreatedDatetimeAsDateTime() < $oldestDate) {
		$oldestDate = $ticket->getCreatedDatetimeAsDateTime();
		$primaryTicket = $ticket;
		$primaryKey = $key;
	}
}
unset($tickets[$primaryKey]);

foreach ($tickets as $ticket) {

	$mergeMessageForPrimaryTicket = "This ticket has been integrated here: <i>" .
		$ticket->getSubjectForMailSubject() . "</i>. <a href='" . $ticket->getLink() . "'>Link to closed original ticket</a>.";

	$primaryTicket->addTicketComment(
		text: $mergeMessageForPrimaryTicket,
		user: login::getUser(),
		facility: EnumTicketCommentFacility::automatic,
		accessLevel: EnumTicketCommentAccessLevel::Public,
	);

	foreach ($ticket->getTicketComments() as $comment) {
		if ($comment->isOfFacilityUser() || $comment->isPublicAccessLevel()) {
			$comment->TicketCommentId = null;
			$comment->Guid = null;
			$comment->TicketId = $primaryTicket->getTicketId(); //map to another ticket
			$clonedTicketComment = TicketCommentController::save($comment);
		}
	}

	foreach ($ticket->getTicketAssociates() as $ta) {
		if (!$primaryTicket->hasTicketAssociateForOrganizationUser($ta->getOrganizationUser()))
			$primaryTicket->addTicketAssociate(
				ouser: $ta->getOrganizationUser(),
				sendMail: true
			);
	}

	//if ticket has files, copy them to primary ticket
	if ($ticket->hasTicketFiles()) {
		foreach ($ticket->getTicketFiles() as $ticketFile) {
			$newTicketFile = new TicketFile(0);
			$newTicketFile->TicketId = $primaryTicket->getTicketId();
			if ($ticketFile->hasUser())
				$newTicketFile->UserId = $ticketFile->getUserId();
			$newTicketFile->FileId = $ticketFile->getFileId();
			$copiedTicketFile = TicketFileController::save($newTicketFile);
		}
	}

	if ($ticket->hasTicketActionItems()) {
		foreach ($ticket->getTicketActionItems() as $tai) {
			$tai->TicketActionItemId = null;
			$tai->Guid = null;
			$tai->TicketId = $primaryTicket->getTicketId();
			$newTai = TicketActionItemController::save($tai);
		}
	}

	$mergeMessage = "This ticket has been merged into the ticket " .
		"<a href='" . $primaryTicket->getLink() . "'>" . $primaryTicket->getTicketNumber() . "</a>";

	$ticket->addTicketComment(
		text: $mergeMessage,
		user: login::getUser()
	);

	//$ticket->close(login::getUser());
	$ticket->setStatus(TicketStatusHelper::getDuplicateStatus(), login::getUser());
}

echo jsonStatus(true, "Tickets merged successfully", $primaryTicket->toJsonObject());

