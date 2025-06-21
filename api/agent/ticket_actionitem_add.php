<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket nicht gefunden."));

if (!isset($_POST["title"]))
	die(jsonStatus(false, "Title nicht gefunden."));

if (!isset($_POST["description"]))
	die(jsonStatus(false, "Description nicht gefunden."));

$ticket = new Ticket($_POST["ticket"]);
$title = $_POST["title"];
$description = $_POST["description"];

$ticketActionItem = $ticket->addCustomTicketActionItem($title, $description);

die(jsonStatus(true, "", $ticketActionItem->toJsonObject()));
