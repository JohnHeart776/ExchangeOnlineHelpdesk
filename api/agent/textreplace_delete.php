<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

$textReplace = new TextReplace($_POST["pk"]);
if (!$textReplace->isValid())
	die(jsonStatus(false, "Text replacement not found."));

$deleteResult = $textReplace->delete();
echo jsonStatus($deleteResult);
