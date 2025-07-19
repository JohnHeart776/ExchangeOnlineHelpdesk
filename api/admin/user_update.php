<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$user = new User($_POST["pk"]);
if (!$user->isValid())
	die(jsonStatus(false, "Benutzer not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $user->toggleValue($name);
} else {
	$updateStatusResult = $user->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $user->toJsonObject());
