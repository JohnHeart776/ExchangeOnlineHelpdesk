<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Check if file was uploaded
if (empty($_FILES['file'])) {
	die(jsonStatus(false, 'No file uploaded'));
}
// Check if file was uploaded
if (empty($_POST['ticket'])) {
	die(jsonStatus(false, 'No ticket provided'));
}

$ticket = new Ticket($_POST['ticket']);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));


// Validate file
$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
	die(jsonStatus(false, 'Error uploading file'));
}

// Check file size (20MB limit)
if ($file['size'] > 20 * 1024 * 1024) {
	die(jsonStatus(false, 'File is too big (max. 20MB)'));
}


// Create file using FileHelper
$file = FileHelper::createFileFromUpload($_FILES['file']);


$ticketFile = new TicketFile(0);
$ticketFile->TicketId = $ticket->getTicketIdAsInt();
$ticketFile->FileId = $file->getFileId();
$ticketFile->AccessLevel = EnumTicketFileAccessLevel::Internal->toString();
$ticketFile->CreatedDatetime = date("Y-m-d H:i:s");
if (login::isLoggedIn())
	$ticketFile->UserId = Login::getUser()->getUserIdAsInt();

$newTicketFile = TicketFileController::save($ticketFile);


$ticket->addTicketComment(
	text: "The file <code>" . $file->getName() . "</code> has been uploaded. Link: <a href='" . $file->getLink() . "'>" . $file->getLink() . "</a>",
	user: login::getUser(),
	facility: EnumTicketCommentFacility::automatic,
	textType: EnumTicketCommentTextType::html,
);


// Return success response
die(jsonStatus(
	true,
	'File was successfully uploaded',
));
