<?php
require_once __DIR__ . '/../src/bootstrap.php';
global $d;
$limit = null;

foreach ($argv as $arg) {
	if (str_starts_with($arg, '--limit=')) {
		$limit = (int)substr($arg, 8);
		break;
	}
}

$sql = "SELECT MailAttachmentId FROM MailAttachment ORDER BY MailAttachmentId";
if ($limit !== null) {
	$sql .= " LIMIT " . $limit;
}

$t = $d->get($sql);

$force = in_array('--force', $argv);

if ($limit !== null) {
	echo "Processing up to {$limit} attachments..." . PHP_EOL;
}

foreach ($t as $u)
{
	$ma = new MailAttachment((int)$u["MailAttachmentId"]);
	
	echo $ma->getName() . PHP_EOL;
	echo $ma->generateTextRepresentation($force) . PHP_EOL;
	echo "--------------".PHP_EOL;
}