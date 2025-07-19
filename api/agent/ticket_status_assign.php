<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$newStatus = new Status($_POST["status"]);
if (!$newStatus->isValid())
	die(jsonStatus(false, "Status ungültig."));

$ticket = $ticket->setStatus($newStatus, login::getUser());

echo jsonStatus(true, "", $ticket->toJsonObject());
