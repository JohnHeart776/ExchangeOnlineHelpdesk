<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireLogin();

if (!isset($_GET["term"]))
	die("Kein Benutzer angegeben.");
$term = $_GET["term"];


if (filter_var($term, FILTER_VALIDATE_EMAIL)) {
	$user = UserController::searchOneBy("Upn", $term);
} else if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $term)) {
	$user = new User($term);
} else {
	$user = UserController::searchOneBy("Upn", $term);
}


$userImage = $user?->getUserImage();

// Bild ausgeben
$data = $userImage->getPhotoDecoded();
header('Content-Type: image/jpeg');
header('Content-Length: ' . strlen($data));
header('Cache-Control: public, max-age=2592000');
header('X-Content-Type-Options: nosniff');
echo $data;
die();
