<?php
require_once __DIR__ . '/../../src/bootstrap.php';

use Core\InputValidator;

Login::requireIsAgent();

// Validate and sanitize the query parameter
$query = InputValidator::getParam('query', FILTER_SANITIZE_SPECIAL_CHARS);

if (empty($query)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing or empty query parameter']);
    exit;
}

// Limit query length to prevent abuse
if (strlen($query) > 100) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Query parameter too long (max 100 characters)']);
    exit;
}

try {
    $users = SearchHelper::searchOrganizationUsers($query);
    
    header('Content-Type: application/json');
    
    // Format the result for Select2
    $response = [
        'results' => array_map(function ($user) {
            return [
                'id' => $user->getGuid(),
                'text' => InputValidator::sanitizeHtml($user->getDisplayName()),
                'image' => $user->getPhotoLink(),
            ];
        }, $users),
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Search operation failed']);
}
