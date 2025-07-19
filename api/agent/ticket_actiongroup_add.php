<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket not found."));

if (!isset($_POST["group"]))
	die(jsonStatus(false, "Actiongroup not selected."));

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$actionGroup = new ActionGroup($_POST["group"]);
if (!$actionGroup->isValid())
	die(jsonStatus(false, "Actiongroup not found."));


$newTicketActionItems = $ticket->addActionGroup($actionGroup);
$r = [];
foreach ($newTicketActionItems as $item) {
	$r[] = $item->toJsonObject();
}

echo jsonStatus(!empty($r), "", ["ticketActionItems" => $r]);
