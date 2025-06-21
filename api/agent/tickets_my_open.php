<?php
require_once __DIR__ . '/../../src/bootstrap.php';
login::requireIsAgent();

$user = login::getUser();
global $d;
$_q = "SELECT TicketId 
FROM Ticket t
LEFT JOIN Status s ON s.StatusId = t.StatusId
WHERE t.AssigneeUserId = ".$user->getUserId()." 
AND s.IsOpen =1
ORDER BY t.DueDatetime ASC";

$t = $d->get($_q);

$tickets = array_map(function ($item) {
	$ticket = new Ticket((int)$item["TicketId"]);
	return $ticket->toJsonObject();
	}, $t);

jsonHeader();
echo json_encode($tickets);

