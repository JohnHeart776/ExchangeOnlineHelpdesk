<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

$r = [];
$colors = [
	['color' => 'primary', 'name' => 'Primary'],
	['color' => 'secondary', 'name' => 'Secondary'],
	['color' => 'success', 'name' => 'Success'],
	['color' => 'danger', 'name' => 'Danger'],
	['color' => 'warning', 'name' => 'Warning'],
	['color' => 'info', 'name' => 'Info'],
	['color' => 'light', 'name' => 'Light'],
	['color' => 'dark', 'name' => 'Dark'],
];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';

foreach ($colors as $color) {
	if ($isEditable) {
		$r[] = [
			'value' => $color['color'],
			'text' => $color['name'],
		];
	} else {
		$r[] = [
			'color' => $color['color'],
			'name' => $color['name'],
		];
	}
}

echo json_encode($r);
