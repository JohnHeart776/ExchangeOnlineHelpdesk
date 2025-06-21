<?php
require_once __DIR__ . '/src/bootstrap.php';

if (!isset($_GET['guid']) || !isset($_GET['secret'])) {
	http_response_code(400);
	die('Missing parameters');
}

$file = new File($_GET['guid']);

if ($file->Secret1 !== $_GET['secret']) {
	http_response_code(403);
	die('Invalid secret');
}

if (isset($_GET['nocache'])) {
	header('Cache-Control: no-store');
} else {
	header('Cache-Control: public, max-age=604800');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
}
header('ETag: "' . $file->HashSha256 . '"');
header('Content-Type: ' . $file->getType());
header('Content-Disposition: inline; filename="' . $file->getName() . '"');

echo $file->getDataDecoded();
