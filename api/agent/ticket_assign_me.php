<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$ticket = $ticket->assignUser(login::getUser());

$defaultStatus = TicketStatusHelper::getDefaultStatus();
$defaultAssignedStatus = TicketStatusHelper::getDefaultAssignedStatus();

if ($ticket->getStatus()->equals($defaultStatus)) {
	//if the ticket is still on initial status
	$ticket->setStatus($defaultAssignedStatus);
}

echo jsonStatus(true, "", $ticket->toJsonObject());
