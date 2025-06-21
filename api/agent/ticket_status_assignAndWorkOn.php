<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket nicht gefunden."));

$ticket->assignUser(login::getUser());
$ticket->setStatus(TicketStatusHelper::getDefaultAssignedStatus()); //assign status to be assigned (so mails are tirggered)

$ticket->setStatus(TicketStatusHelper::getDefaultWorkingStatus()); //assign working on status

$ticket->spawn(); //refresh ticket object just be sure

echo jsonStatus(true, "", $ticket->toJsonObject());
