<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticketactionitem"]))
	die(jsonStatus(false, "Ticket nicht gefunden."));

$tai = new TicketActionItem($_POST["ticketactionitem"]);
if (!$tai->isValid())
	die(jsonStatus(false, "TicketActionItem nicht gefunden."));

$tai->toggleCompleted();

die(jsonStatus(true, "", $tai->toJsonObject()));
