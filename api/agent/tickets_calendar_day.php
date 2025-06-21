<?php
require_once __DIR__ . '/../../src/bootstrap.php';
login::requireIsAgent();

$date = $_POST['date'] ?? null;
if (!$date) {
	die(json_encode([
		'status' => false,
		'message' => 'Date parameter is required',
	]));
}

$user = login::getUser();
global $d;
$_q = "SELECT TicketId 
FROM Ticket 
WHERE AssigneeUserId = ".$user->getUserId()." 
AND DATE(CreatedDatetime) = DATE(\"".$d->filter($date)."\") 
ORDER BY CreatedDatetime ASC";

$t = $d->get($_q);

$tickets = array_map(function ($item) {
	$ticket = new Ticket((int)$item["TicketId"]);
	return $ticket->toJsonObject();
	}, $t);

jsonHeader();
echo json_encode($tickets);

