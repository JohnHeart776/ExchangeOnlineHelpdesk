<?php
require_once __DIR__ . '/src/bootstrap.php';

if (!isset($_GET["mode"]))
	die();

if (!isset($_GET["guid"]))
	die(jsonStatus(false, "No guid provided"));
$guid = $_GET["guid"];

if (!isset($_GET["secret"]))
	die(jsonStatus(false, "No secret provided"));
$secret = $_GET["secret"];

$mailAttachment = MailAttachmentController::searchOneBy("Guid", $guid);
if (!$mailAttachment)
	die(jsonStatus(false, "No mail attachment found"));

if ($_GET["mode"] == "download") {

	if ($mailAttachment->getSecret1() != $secret)
		die(jsonStatus(false, "Invalid secret for Operation"));

// Base64-dekodierten Inhalt vorbereiten
	$content = $mailAttachment->getContentDecoded();
	$contentLength = strlen($content);

// Header senden
	header('Content-Type: ' . $mailAttachment->getContentType());
	header('Content-Length: ' . $contentLength);
	header('Content-Disposition: inline; filename="' . basename($mailAttachment->getName()) . '"');
	header('Cache-Control: private, max-age=3600');
	header('X-Content-Type-Options: nosniff');

	echo $content;
}

if ($_GET["mode"] == "text") {
	if ($mailAttachment->getSecret2() != $secret)
		die(jsonStatus(false, "Invalid secret for Operation"));

	if ($mailAttachment->hasTextRepresentation())
		echo $mailAttachment->getTextRepresentation();
}

die();
