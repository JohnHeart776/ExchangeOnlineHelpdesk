<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

// Validate required parameters
if (!isset($_POST['name'])) {
	echo jsonStatus(false);
}

try {
	$template = new NotificationTemplate(0);
	$template->Name = $_POST['name'];
	$template->Enabled = 0;

	// Save the notification template
	$newTemplate = NotificationTemplateController::save($template);

	echo jsonStatus(true, "", ["template" => $newTemplate->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false);
}
