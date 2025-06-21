<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$template = new NotificationTemplate($_POST["pk"]);
if (!$template->isValid())
	die(jsonStatus(false, "Benachrichtigungsvorlage nicht gefunden."));

$name = $_POST["name"];
$value = $_POST["value"];

try {
	if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
		$updateResult = $template->toggleValue($name);
	} else {
		$updateResult = $template->update($name, $value);
	}

	if (!$updateResult) {
		die(jsonStatus(false, "Benachrichtigungsvorlage konnte nicht aktualisiert werden."));
	}

	echo jsonStatus(true, "", $template->toJsonObject());
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}
