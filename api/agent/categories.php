<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';
$isSelect2 = isset($_GET['format']) && $_GET['format'] === 'select2';

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$categories = ($searchTerm && strlen($searchTerm) >= 3) ? CategoryController::searchBy("PublicName", "%$searchTerm%") : CategoryController::getAll();
usort($categories, fn($a, $b) => strcmp($a->getPublicName(), $b->getPublicName()));


foreach ($categories as $category) {
	if ($isSelect2) {
		$r['results'][] = [
			'id' => $category->getCategoryIdAsInt(),
			'text' => $category->getPublicName(),
		];
	} else if ($isEditable) {
		$r[] = [
			'value' => $category->getCategoryIdAsInt(),
			'text' => $category->getPublicName(),
		];
	} else {
		$r[] = $category->toJsonObject();
	}
}
echo json_encode($r);
