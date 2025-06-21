<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

header('Content-Type: application/json');

if (!isset($_GET['guid'])) {
	http_response_code(400);
	die(json_encode(['error' => 'Missing required parameter: guid']));
}

$article = new Article($_GET['guid']);
echo json_encode($article->toJsonObject());

