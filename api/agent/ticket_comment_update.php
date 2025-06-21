<?php
require_once __DIR__ . '/../../src/bootstrap.php';

// Ensure user is logged in as agent
Login::requireIsAgent();

$TicketComment = new TicketComment($_POST["pk"]);
if (!$TicketComment->isValid())
	die(jsonStatus(false, "TicketComment not found."));

$name = $_POST["name"];
$value = $_POST["value"];


if ($name == "Text") {
	if ($TicketComment->getIsEditableAsBool() != true)
		throw new Exception("Trying to Update the Text of a Ticket Comment that is not editable");
	//switch to text type html
	$TicketComment->update("TextType", EnumTicketCommentTextType::html->toString());
}

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $TicketComment->toggleValue($name);
} else {
	$updateStatusResult = $TicketComment->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $TicketComment->toJsonObject());
