<?php
require_once __DIR__ . '/../../src/bootstrap.php';

// Require agent login
Login::requireIsAgent();

// Check if prompt is set
if (!isset($_POST['prompt']) || empty($_POST['prompt'])) {
	die(jsonStatus(false, "No prompt provided"));
}

// Check if AI is enabled in config
if (!Config::getConfigValueFor('ai.enable')) {
	die(jsonStatus(true, "ich kann keinen text generieren"));
}

$prompt = $_POST['prompt'];
$prompt = PromptHelper::cleanupPrompt($prompt);

try {
	$prompt = $_POST["prompt"];
	$content = AiService::getRepsonse($prompt);

	if (!$content) {
		die(jsonStatus(false, "AI response not valid."));
	}

	echo jsonStatus(true, "", ['text' => $content]);
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}
