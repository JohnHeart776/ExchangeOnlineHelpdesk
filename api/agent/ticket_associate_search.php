<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$users = SearchHelper::searchOrganizationUsers($_GET["query"]);

header('Content-Type: application/json');

// Format the result for Select2
$response = [
	'results' => array_map(function ($user) {
		return [
			'id' => $user->getGuid(),          // Assuming $user array contains 'id'
			'text' => $user->getDisplayName(), // Assuming $user array contains 'name'
			'image' => $user->getPhotoLink(),  // Assuming $user array contains 'name'
		];
	}, $users),
];

echo json_encode($response);
