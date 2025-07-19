<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$config = new Config($_POST["pk"]);
if (!$config->isValid())
	die(jsonStatus(false, "Config not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $config->toggleValue($name);
} else {
	$updateStatusResult = $config->update("Value", $value);
}

$config->spawn();
echo jsonStatus($updateStatusResult, "", $config->toJsonObject());
