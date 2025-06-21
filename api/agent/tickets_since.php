<?php

use Database\Database;

require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

header('Content-Type: application/json');

$since = $_GET["since"];
if (!$since) {
	echo json_encode(['newTicketsAvailable' => false]);
	exit;
}

global $d;
$_q = "SELECT TicketId FROM Ticket WHERE TicketNumber = :tn";
$t = $d->getPDO($_q, ["tn" => $since], true);
if (empty($t)) {
	echo json_encode(['newTicketsAvailable' => false]);
	die();
}

$ticketIdSince = $t["TicketId"];

$_q = "SELECT count(TicketId) as a
FROM Ticket t
LEFT JOIN Status s ON t.StatusId = s.StatusId  
WHERE t.TicketId > $ticketIdSince 
AND s.IsFinal IS NULL";

$t = $d->get($_q, true);
$a = (int)$t["a"];

if ($a <= 0) {
	echo json_encode(['newTicketsAvailable' => false]);
} else {
	echo json_encode(['newTicketsAvailable' => true]);
}
die();
