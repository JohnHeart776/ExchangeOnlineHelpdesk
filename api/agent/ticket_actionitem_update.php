<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticketActionItem = new TicketActionItem($_POST["pk"]);
if (!$ticketActionItem->isValid())
	die(jsonStatus(false, "Ticket-Aktionspunkt nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $ticketActionItem->toggleValue($name);
} else {
	$updateStatusResult = $ticketActionItem->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $ticketActionItem->toJsonObject());
