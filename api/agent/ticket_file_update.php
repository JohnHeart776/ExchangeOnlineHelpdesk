<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$TicketFile = new TicketFile($_POST["pk"]);
if (!$TicketFile->isValid())
	die(jsonStatus(false, "Template text not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $TicketFile->toggleValue($name);
} else {
	$updateStatusResult = $TicketFile->update($name, $value);
}
echo jsonStatus($updateStatusResult, "", $TicketFile->toJsonObject());
