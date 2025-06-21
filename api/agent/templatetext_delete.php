<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

$templateText = new TemplateText($_POST["pk"]);
if (!$templateText->isValid())
	die(jsonStatus(false, "Template text not found."));

$deleteResult = $templateText->delete();
echo jsonStatus($deleteResult);
