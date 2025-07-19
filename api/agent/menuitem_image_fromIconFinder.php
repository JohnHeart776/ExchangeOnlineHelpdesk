<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["guid"])) {
	die(jsonStatus(false, "Parameter 'guid' is required."));
}

$menuItem = new MenuItem($_POST["guid"]);
if (!$menuItem->isValid())
	die(jsonStatus(false, "Menüelement not found."));


try {
	$client = new IconfinderClient(
		Config::getConfigValueFor("iconfinder.api.clientid"),
		Config::getConfigValueFor("iconfinder.api.key"),
	);
	$blob = $client->getFirstFreeIconBlob($menuItem->getTitle());
} catch (RuntimeException $e) {
	die(jsonStatus(false, $e->getMessage()));
}

$name = "icon_" . uniqid() . "_" . slugify($menuItem->getTitle()) . ".png";
$type = "image/png";
$newFile = FileHelper::createFileFromString($name, $type, $blob);

$menuItem->update("ImageFileId", $newFile->getFileIdAsInt());

echo jsonStatus($newFile->isValid(), "", $newFile->toJsonObject());
