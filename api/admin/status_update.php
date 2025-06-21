<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$status = new Status($_POST["pk"]);
if (!$status->isValid())
	die(jsonStatus(false, "Status nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

try {
	if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
		$updateResult = $status->toggleValue($name);
	} else {
		$updateResult = $status->update($name, $value);
	}

	if (!$updateResult) {
		die(jsonStatus(false, "Status konnte nicht aktualisiert werden."));
	}

	echo jsonStatus(true, "", $status->toJsonObject());
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}
