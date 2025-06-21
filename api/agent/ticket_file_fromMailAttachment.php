<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Check if mail attachment GUID was provided
if (empty($_POST['ticket'])) {
	die(jsonStatus(false, 'No ticket GUID provided'));
}
// Check if ticket was provided
if (empty($_POST['mailattachment'])) {
	die(jsonStatus(false, 'No mail attachment GUID provided'));
}

$ticket = new Ticket($_POST['ticket']);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));


$mailAttachment = new MailAttachment($_POST["mailattachment"]);
if (!$mailAttachment->isValid())
	die(jsonStatus(false, "Mail attachment not found."));


// Create file from mail attachment
try {
	$file = FileHelper::createFileFromMailAttachment($mailAttachment);
} catch (Exception $e) {
	die(jsonStatus(false, 'Error processing mail attachment: ' . $e->getMessage()));
}


$ticketFile = new TicketFile(0);
$ticketFile->TicketId = $ticket->getTicketIdAsInt();
$ticketFile->FileId = $file->getFileId();
$ticketFile->AccessLevel = EnumTicketFileAccessLevel::Internal->toString();
$ticketFile->CreatedDatetime = date("Y-m-d H:i:s");
$ticketFile->AccessLevel = "Public";
if (login::isLoggedIn())
	$ticketFile->UserId = Login::getUser()->getUserIdAsInt();

$newTicketFile = TicketFileController::save($ticketFile);


$ticket->addTicketComment(
	text: "The mail attachment <code>" . $file->getName() . "</code> has been copied to a file. <a href='" . $file->getLink() . "'>Link to file</a>",
	user: login::getUser(),
	facility: EnumTicketCommentFacility::automatic,
	textType: EnumTicketCommentTextType::html,
);


// Return success response
die(jsonStatus(
	true,
	'Mail attachment successfully added',
));
