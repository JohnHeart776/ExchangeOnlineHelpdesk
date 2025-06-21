<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$actionGroup = new ActionGroup($_POST["pk"]);
if (!$actionGroup->isValid())
	die(jsonStatus(false, "Aktionsgruppe nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $actionGroup->toggleValue($name);
} else {
	$updateStatusResult = $actionGroup->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $actionGroup->toJsonObject());
