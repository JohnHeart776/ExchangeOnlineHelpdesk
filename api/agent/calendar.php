<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();


// Get start and end dates from GET parameters
$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';

$startDateTime = new DateTime($start);
$endDateTime = new DateTime($end);

global $d;

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

$t = $d->getPDO($query, $params);
$tickets = array_map(function ($t) {
	return new Ticket((int)$t['TicketId']);
}, $t);
$events = [];

foreach ($tickets as $ticket) {

	$events[] = [
		'id' => $ticket->getGuid(),
		'title' => $ticket->getSubjectForMailSubject(false, 30),
		'start' => $ticket->getDueDatetimeAsDateTime()->format('Y-m-d\TH:i:s'),
		'end' => $ticket->getDueDatetimeAsDateTime()->add(new DateInterval("PT1H"))->format('Y-m-d\TH:i:s'),
		'url' => $ticket->getLink(),
//			'backgroundColor' => $ticket->getCategory()->getColor(),
		'textColor' => "#000",
		'backgroundColor' => "#fff",
//			'borderColor' => $ticket['status_color'],
		'borderColor' => "#000",
	];

}

header('Content-Type: application/json');
echo json_encode($events);
