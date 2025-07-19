<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$ticket->setNull("AssigneeUserId");
$ticket->setStatus(TicketStatusHelper::getDefaultStatus());
$ticket->spawn();

echo jsonStatus(true, "", $ticket->toJsonObject());
