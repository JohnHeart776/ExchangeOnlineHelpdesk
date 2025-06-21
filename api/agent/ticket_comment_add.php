<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket nicht gefunden."));

if (!isset($_POST["editor0"]))
	die(jsonStatus(false, "Editor0 nicht gefunden."));

$html = $_POST["editor0"];

$type = "internal";
if (isset($_POST["type"])) {
	//allowed destinations
	$destinations = ["internal", "external"];
	if (in_array($_POST["type"], $destinations))
		$type = $_POST["type"];
	else
		throw new Exception("Destinations not allowed");
}

$accessLevel = "Internal";
if (isset($_POST["accesslevel"])) {

	$accessLevels = ["Internal", "Public"];

	if (in_array($_POST["accesslevel"], $accessLevels))
		$accessLevel = $_POST["accesslevel"];
	else
		throw new Exception("Desired Accesslevel not allowed");

}


$ticketComment = $ticket->addTicketComment(
	text: $html,
	user: login::getUser(),
	facility: EnumTicketCommentFacility::user,
	textType: EnumTicketCommentTextType::html,
	accessLevel: EnumTicketCommentAccessLevel::fromString($accessLevel),
);

//only send to external associates if type is external
$mailResult = true;

if ($type == "external") {
	$mailResult = true;
	foreach ($ticket->getTicketAssociates() as $ticketAssociate) {
		$mailResult = $mailResult && $ticketAssociate->sendTicketCommentAsMailMessage($ticketComment);
	}

	$ticket->setStatus(
		status: TicketStatusHelper::getDefaultWaitingForCustomerStatus(),
	);

}


echo jsonStatus($mailResult, "", ["mailResult" => $mailResult, "ticketComment" => $ticketComment->toJsonObject()]);
