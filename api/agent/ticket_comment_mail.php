<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$tc = new TicketComment($_POST["ticketcomment"]);

if (!$tc->isValid())
	die(jsonStatus(false, "Ticketcomment not found."));

if (!$tc->hasMail())
	die(jsonStatus(false, "No Mail Body found."));

echo jsonStatus(true, "", $tc->getMail()->exportAsJson());
