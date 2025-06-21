<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

try {
	$templateTexts = TemplateTextController::getAll();
	$jsonResponse = array_map(function ($template) {
		return $template->toJsonObject();
	}, $templateTexts);

	echo json_encode(['success' => true, 'templates' => $jsonResponse]);
} catch (Exception $e) {
	echo jsonStatus(false, $e->getMessage());
}

