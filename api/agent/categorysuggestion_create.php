<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Login::requireIsAgent();

$suggestion = new CategorySuggestion(0);
$suggestion->Enabled = 1;
$suggestion->Priority = 1;
$suggestion->AutoClose = 0;
$suggestion->Filter = $_POST['filter'];

$newSuggestion = CategorySuggestionController::save($suggestion);
if ($newSuggestion->isValid()) {
	echo jsonStatus(true, "", ["suggestion" => $newSuggestion->toJsonObject()]);
} else {
	echo jsonStatus(false);
}

