<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';
$isSelect2 = isset($_GET['format']) && $_GET['format'] === 'select2';

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$shouldSearch = $searchTerm && strlen($searchTerm) >= 3;
$ousers = $shouldSearch ? OrganizationUserController::searchBy("DisplayName", "%$searchTerm%") : [];
usort($ousers, fn($a, $b) => strcmp($a->getDisplayName(), $b->getDisplayName()));

foreach ($ousers as $ouser) {

	if ($isSelect2) {
		$r['results'][] = [
			'id' => $ouser->getOrganizationUserId(),
			'text' => $ouser->getDisplayName(),
			'image' => $ouser->getAvatarLink(),
		];
	} else if ($isEditable) {
		$r[] = [
			'value' => $ouser->getOrganizationUserId(),
			'text' => $ouser->getDisplayName(),
		];
	} else {
		$r[] = $ouser->toJsonObject();
	}
}
echo json_encode($r);
