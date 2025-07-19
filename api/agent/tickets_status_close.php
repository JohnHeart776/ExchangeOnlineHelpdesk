<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["tickets"]))
	die(jsonStatus(false, "Tickets not found."));

$solve = 0; //default solved state
if (isset($_GET["solve"]) && $_GET["solve"] == "1")
	$solve = 1;

$tickets = [];
foreach ($_POST["tickets"] as $k => $t) {
	$ticket = new Ticket($t);
	if (!$ticket->isValid())
		die(jsonStatus(false, "Ticket in index $k ($t) not found."));
	$tickets[] = $ticket;
}

$assignMe = (isset($_POST["assignMe"]) && !empty($_POST["assignMe"]));

if ($solve === 0)
	$desiredNewStatus = TicketStatusHelper::getDefaultClosedStatus();
else if ($solve === 1)
	$desiredNewStatus = TicketStatusHelper::getDefaultResolvedStatus();
else
	throw new Exception("Invalid solve value");


if (!$desiredNewStatus)
	die(jsonStatus(false, "Internal error: Closed status not found."));

foreach ($tickets as $ticket) {
	if ($assignMe) {
		$ticket = $ticket->assignUser(Login::getUser());
		if ($solve === 1) //only set the status (plus the mail sending when we're solving)
			$ticket->setStatus(TicketStatusHelper::getDefaultAssignedStatus());
	}

	$ticket = $ticket->setStatus($desiredNewStatus);
}

echo jsonStatus(true, "", array_map(fn($t) => $t->toJsonObject(), $tickets));
