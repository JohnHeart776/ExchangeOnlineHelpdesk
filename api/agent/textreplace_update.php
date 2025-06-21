<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$textReplacement = new TextReplace($_POST["pk"]);
if (!$textReplacement->isValid())
	die(jsonStatus(false, "Text replacement not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $textReplacement->toggleValue($name);
} else {
	$updateStatusResult = $textReplacement->update($name, $value);
}
echo jsonStatus($updateStatusResult, "", $textReplacement->toJsonObject());
