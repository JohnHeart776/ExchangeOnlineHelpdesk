<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket nicht gefunden."));

if (!isset($_POST["group"]))
	die(jsonStatus(false, "Aktionsgruppe nicht ausgewählt."));

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket nicht gefunden."));

$actionGroup = new ActionGroup($_POST["group"]);
if (!$actionGroup->isValid())
	die(jsonStatus(false, "Aktionsgruppe nicht gefunden."));


$newTicketActionItems = $ticket->addActionGroup($actionGroup);
$r = [];
foreach ($newTicketActionItems as $item) {
	$r[] = $item->toJsonObject();
}

echo jsonStatus(!empty($r), "", ["ticketActionItems" => $r]);
