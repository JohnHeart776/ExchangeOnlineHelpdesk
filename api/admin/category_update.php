<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

$category = new Category($_POST["pk"]);
if (!$category->isValid())
	die(jsonStatus(false, "Kategorie not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $category->toggleValue($name);
} else {
	$updateStatusResult = $category->update($name, $value);
}

echo jsonStatus($updateStatusResult, "", $category->toJsonObject());
