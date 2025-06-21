<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';
$isSelect2 = isset($_GET['format']) && $_GET['format'] === 'select2';

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$users = ($searchTerm && strlen($searchTerm) >= 3) ? UserController::searchBy("DisplayName", "%$searchTerm%") : UserController::getAll();

usort($users, function ($a, $b) {
	return strcmp($a->getDisplayName(), $b->getDisplayName());
});

foreach ($users as $user) {
	if (!$user->isAgent())
		continue;
	if (!$user->isEnabled())
		continue;

	if ($isSelect2) {
		$r['results'][] = [
			'id' => $user->getUserIdAsInt(),
			'text' => $user->getDisplayName(),
			'image' => $user->getUserImageLink(),
		];
	} else if ($isEditable) {
		$r[] = [
			'value' => $user->getUserIdAsInt(),
			'text' => $user->getDisplayName(),
		];
	} else {
		$r[] = $user->toJsonObject();
	}
}
echo json_encode($r);
