<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['name'], $_POST['description'])) {
	echo jsonStatus(false, "Missing required parameters");
}

try {
	$templateText = new TemplateText(0);
	$templateText->Name = $_POST['name'];
	$templateText->Description = $_POST['description'];

	// Save the template text
	$newTemplateText = TemplateTextController::save($templateText);

	echo jsonStatus(true, "Template text created successfully", ["template" => $newTemplateText->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false, $e->getMessage());
}

