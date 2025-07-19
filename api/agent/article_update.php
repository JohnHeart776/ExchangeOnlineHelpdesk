<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$article = new Article($_POST["pk"]);
if (!$article->isValid())
	die(jsonStatus(false, "Artikel not found."));

$name = $_POST["name"];
$value = $_POST["value"];

if (isset($_POST["action"]) && $_POST["action"] === "toggle") {
	$updateStatusResult = $article->toggleValue($name);
} else {
	$updateStatusResult = $article->update($name, $value);
	$updateStatusResult = $article->update("UpdatedAtDatetime", date("Y-m-d H:i:s"));
}

echo jsonStatus($updateStatusResult, "", $article->toJsonObject());
