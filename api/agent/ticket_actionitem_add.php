<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket not found."));

if (!isset($_POST["title"]))
	die(jsonStatus(false, "Title not found."));

if (!isset($_POST["description"]))
	die(jsonStatus(false, "Description not found."));

$ticket = new Ticket($_POST["ticket"]);
$title = $_POST["title"];
$description = $_POST["description"];

$ticketActionItem = $ticket->addCustomTicketActionItem($title, $description);

die(jsonStatus(true, "", $ticketActionItem->toJsonObject()));
