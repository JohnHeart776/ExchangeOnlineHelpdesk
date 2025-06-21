<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';
$isSelect2 = isset($_GET['format']) && $_GET['format'] === 'select2';

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$articles = ($searchTerm && strlen($searchTerm) >= 3) ? ArticleController::searchBy("Title", "%$searchTerm%") : ArticleController::getAll();

foreach ($articles as $article) {

	if ($isSelect2) {
		$r['results'][] = [
			'id' => $article->getGuid(),
			'text' => $article->getTitle(),
		];
	} else if ($isEditable) {
		$r[] = [
			'value' => $article->getGuid(),
			'text' => $article->getTitle(),
		];
	} else {
		$r[] = $article->toJsonObject();
	}
}
echo json_encode($r);
