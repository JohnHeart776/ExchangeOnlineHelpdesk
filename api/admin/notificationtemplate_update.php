<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$template = new NotificationTemplate($_POST["pk"]);
if (!$template->isValid())
	die(jsonStatus(false, "Notification template not found."));

$name = $_POST["name"];
$value = $_POST["value"];

try {
	if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
		$updateResult = $template->toggleValue($name);
	} else {
		$updateResult = $template->update($name, $value);
	}

	if (!$updateResult) {
		die(jsonStatus(false, "Failed to update notification template."));
	}

	echo jsonStatus(true, "", $template->toJsonObject());
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}
