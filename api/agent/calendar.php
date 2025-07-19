<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

// Validate and sanitize input parameters
$start = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_STRING);
$end = filter_input(INPUT_GET, 'end', FILTER_SANITIZE_STRING);

if (empty($start) || empty($end)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required parameters: start and end']);
    exit;
}

// Create DateTime objects with error handling
try {
    $startDateTime = new DateTime($start);
    $endDateTime = new DateTime($end);
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// Use dependency injection instead of global variable
$database = Database::getInstance();

// Query tickets within date range
$query = "SELECT t.TicketId 
FROM Ticket t
LEFT JOIN Status s ON s.StatusId = t.StatusId 
WHERE t.DueDatetime >= :start 
  AND t.DueDatetime <= :end
  AND (s.IsFinal IS NULL OR s.IsFinal = 0)";

$params = [
	'start' => $startDateTime->format(DateTimeImmutable::ATOM),
	'end' => $endDateTime->format(DateTimeImmutable::ATOM),
];

$ticketResults = $database->getPDO($query, $params);
$tickets = array_map(function ($ticketData) {
	return new Ticket((int)$ticketData['TicketId']);
}, $ticketResults);
$events = [];

foreach ($tickets as $ticket) {
	$events[] = [
		'id' => $ticket->getGuid(),
		'title' => $ticket->getSubjectForMailSubject(false, 30),
		'start' => $ticket->getDueDatetimeAsDateTime()->format('Y-m-d\TH:i:s'),
		'end' => $ticket->getDueDatetimeAsDateTime()->add(new DateInterval("PT1H"))->format('Y-m-d\TH:i:s'),
		'url' => $ticket->getLink(),
		'textColor' => "#000",
		'backgroundColor' => "#fff",
		'borderColor' => "#000",
	];
}

header('Content-Type: application/json');
echo json_encode($events);
