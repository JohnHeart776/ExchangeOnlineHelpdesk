<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$solve = 0; //default solved state
if (isset($_GET["solve"]) && $_GET["solve"] == "1")
	$solve = 1;

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket nicht gefunden."));

if (isset($_POST["assignme"]) && !empty($_POST["assignme"])) {
	$ticket = $ticket->assignUser(Login::getUser());
}

if (!$ticket->hasAssignee()) //if there is no assignee, assign current user
{
	$ticket->assignUser(login::getUser());
	if ($solve ===1) //only set the status (plus the mail sending when we're solving)
		$ticket->setStatus(TicketStatusHelper::getDefaultAssignedStatus());
}

if ($solve === 0)
	$desiredNewStatus = TicketStatusHelper::getDefaultClosedStatus();
else if ($solve === 1)
	$desiredNewStatus = TicketStatusHelper::getDefaultResolvedStatus();
else
	throw new Exception("Invalid solve value");


if (!$desiredNewStatus->isValid())
	die(jsonStatus(false, "Status ungültig."));

$ticket = $ticket->setStatus($desiredNewStatus);

echo jsonStatus(true, "", $ticket->toJsonObject());
