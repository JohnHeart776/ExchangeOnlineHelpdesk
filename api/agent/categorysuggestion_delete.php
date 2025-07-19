<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$categorySuggestion = new CategorySuggestion($_POST["pk"]);
if (!$categorySuggestion->isValid())
	die(jsonStatus(false, "Kategorieempfehlung not found."));

$deleteResult = $categorySuggestion->delete();
echo jsonStatus($deleteResult);
