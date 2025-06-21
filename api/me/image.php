<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireLogin();
$user = Login::getUser();
if (!$user) {
	http_response_code(403);
	exit('Kein Benutzerbild verfügbar.');
}

$userImage = $user->getUserImage();
$data = $userImage->getPhotoDecoded();

header('Cache-Control: public');
header('Pragma: public');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 24 * 365) . ' GMT');
header('Content-Type: image/jpeg');
header('Content-Length: ' . strlen($data));
echo $data;
die();
