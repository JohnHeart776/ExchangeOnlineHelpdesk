<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$categorySuggestion = new CategorySuggestion($_POST["pk"]);
if (!$categorySuggestion->isValid())
	die(jsonStatus(false, "Kategorieempfehlung not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $categorySuggestion->toggleValue($name);
} else {
	$updateStatusResult = $categorySuggestion->update($name, $value);
}
echo jsonStatus($updateStatusResult, "", $categorySuggestion->toJsonObject());
