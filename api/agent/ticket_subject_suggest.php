<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Missing Ticket Guid"));

$ticket = new Ticket($_POST["ticket"]);

if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket is invalid"));

if (!$ticket->hasMails())
	die(jsonStatus(false, "This function is not possible for this ticket."));

$subject = $ticket->getFirstMailForTicket()->suggestSubjectWithAi();

die(jsonStatus(true, "", ["subject" => $subject]));