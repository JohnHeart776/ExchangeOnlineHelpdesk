<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$menuItem = new MenuItem($_POST["pk"]);
if (!$menuItem->isValid())
	die(jsonStatus(false, "Menüelement nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $menuItem->toggleValue($name);
} else {
	$updateStatusResult = $menuItem->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $menuItem->toJsonObject());
