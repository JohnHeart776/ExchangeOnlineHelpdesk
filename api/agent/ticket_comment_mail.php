<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$tc = new TicketComment($_POST["ticketcomment"]);

if (!$tc->isValid())
	die(jsonStatus(false, "Ticketcomment nicht gefunden."));

if (!$tc->hasMail())
	die(jsonStatus(false, "Kein Mail-Body vorhanden."));

echo jsonStatus(true, "", $tc->getMail()->exportAsJson());
