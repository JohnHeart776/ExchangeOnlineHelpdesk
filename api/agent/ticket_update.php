<?php
require_once __DIR__ . '/../../src/bootstrap.php';

// Ensure user is logged in as agent
Login::requireIsAgent();

$ticket = new Ticket($_POST["pk"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $ticket->toggleValue($name);
} else {
	$updateStatusResult = $ticket->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $ticket->toJsonObject());
