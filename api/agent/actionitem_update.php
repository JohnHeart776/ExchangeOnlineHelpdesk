<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$actionItem = new ActionItem($_POST["pk"]);
if (!$actionItem->isValid())
	die(jsonStatus(false, "Aktionselement nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $actionItem->toggleValue($name);
} else {
	$updateStatusResult = $actionItem->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $actionItem->toJsonObject());
