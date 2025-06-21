<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_GET['guid'])) {
	echo jsonStatus(false, "Missing template GUID");
	exit;
}

try {
	$templateText = new TemplateText($_GET['guid']);
	if (!$templateText->isValid()) {
		echo jsonStatus(false, "Template text not found");
		exit;
	}
	$r = $templateText->toJsonObject();
	$r["content"] = $templateText->getContent();
	$r["contentForTinyMce"] = $templateText->getContentForTinyMce();

	echo json_encode($r);
} catch (Exception $e) {
	echo jsonStatus(false, $e->getMessage());
}
