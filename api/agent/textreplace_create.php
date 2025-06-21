<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Login::requireIsAgent();

$replacement = new TextReplace(0);
$replacement->Enabled = 0;
$replacement->SearchFor = $_POST['searchFor'];
$replacement->ReplaceBy = "";

$newReplacement = TextReplaceController::save($replacement);
if ($newReplacement->isValid()) {
	echo jsonStatus(true, "", ["textreplace" => $newReplacement->toJsonObject()]);
} else {
	echo jsonStatus(false);
}

