<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST['guid']);
if (!$ticket->isValid()) {
	die(jsonStatus(false, "Ticket not found."));
}

$newTicket = $ticket->copy();
echo jsonStatus(true, "", $newTicket->toJsonObject());
