<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';

$stati = StatusController::getAll();
usort($stati, function ($a, $b) {
	return strcmp($a->getPublicName(), $b->getPublicName());
});


foreach ($stati as $status) {
	if ($isEditable) {
		$r[] = [
			'value' => $status->getColor(),
			'text' => $status->getPublicName(),
		];
	} else {
		$r[] = $status->toJsonObject();
	}
}
echo json_encode($r);
