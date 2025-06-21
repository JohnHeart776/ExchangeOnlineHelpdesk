<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

// Validate required parameters
if (!isset($_POST['guid'])) {
	echo jsonStatus(false);
}

$ma = new MailAttachment($_POST["guid"]);
if (!$ma->isValid())
	die(jsonStatus(false, "Mail attachment not found."));

//check if this hash is already ignored
if (MailAttachmentIgnore::isMailAttachmentIgnored($ma))
	die(jsonStatus(true, "Mail attachment already ignored."));


$maie = $ma->toMailAttachmentIgnoreEnvelope();
$mai = MailAttachmentIgnoreController::save($maie);

die(jsonStatus(true, "Mail attachment ignored.", $mai->toJsonObject()));