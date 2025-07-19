<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["guid"])) {
	die(jsonStatus(false, "Parameter 'guid' is required."));
}

$menuItem = new MenuItem($_POST["guid"]);
if (!$menuItem->isValid())
	die(jsonStatus(false, "Menüelement not found."));

$client = new NounProjectClient(
	Config::getConfigValueFor("noun.api.key"),
	Config::getConfigValueFor("noun.api.secret"),
);

try {
	$blob = $client->fetchIcon($menuItem->getTitle(), 'svg');
} catch (RuntimeException $e) {
	die(jsonStatus(false, $e->getMessage()));
}

$name = "icon_" . uniqid() . "_" . slugify($menuItem->getTitle()) . ".svg";
$type = "image/svg+xml";
$newFile = FileHelper::createFileFromString($name, $type, $blob);

$menuItem->update("ImageFileId", $newFile->getFileIdAsInt());

echo jsonStatus($newFile->isValid(), "", $newFile->toJsonObject());
