<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireLogin();

if (!isset($_GET["term"]))
	die("Kein Benutzer angegeben.");
$term = $_GET["term"];


if (filter_var($term, FILTER_VALIDATE_EMAIL)) {
	$ouser = OrganizationUserController::searchBy("UserPrincipalName", $term, true);
} else if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $term)) {
	$ouser = new OrganizationUser($term);
} else {
	$ouser = OrganizationUserController::searchBy("UserPrincipalName", $term, true);
}

if (!$ouser?->hasPhoto()) {
	$localPart = explode('@', $term)[0] ?? '';
	$parts = preg_split('/[\.\-_]+/', $localPart); // trenne bei Punkt, Minus, Unterstrich

	if (count($parts) >= 2) {
		$firstName = ucfirst($parts[0]);
		$lastName = ucfirst($parts[1]);
	} else if (strlen($localPart) >= 2) {
		$firstName = strtoupper($localPart[0]);
		$lastName = strtoupper($localPart[1]);
	} else {
		$firstName = 'John';
		$lastName = 'Doe';
	}

	$fullName = "$firstName $lastName";
	$bgColor = substr(md5($fullName), 0, 6);
	$textColor = "ffffff";

	$url = "https://avatar.pulse.one/api/?name=" . urlencode($fullName) . "&background={$bgColor}&color={$textColor}";
	header('Cache-Control: public, max-age=3600');
	header("Location: $url", true, 302);
	die();
}

// Bild ausgeben
$data = $ouser->getPhotoDecoded();
header('Content-Type: image/jpeg');
header('Content-Length: ' . strlen($data));
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
echo $data;
exit;
