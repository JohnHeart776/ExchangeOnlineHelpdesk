<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

if (!isset($_POST["guid"]))
	die(jsonStatus(false, "Parameter 'guid' is required."));

$category = new Category($_POST["guid"]);
if (!$category->isValid())
	die(jsonStatus(false, "Kategorie not found."));

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($limit < 1 || $limit > 1000) {
	$limit = 100;
}

global $d;
$lastGuid = isset($_POST['last']) ? $_POST['last'] : null;

$_q = "SELECT TicketId FROM Ticket WHERE CategoryId =".$category->getCategoryId()." ";

if ($lastGuid) {
	$_q1 = "SELECT TicketId FROM Ticket WHERE Guid = \"".$d->filter($lastGuid)."\"";
	$lastTicket = $d->get($_q1, true);
	$lastTicketId = (int)$lastTicket["TicketId"];

	$_q .= "AND TicketId > $lastTicketId ";;
	$params[] = $lastGuid;
}

$_q .= "ORDER BY TicketId ASC LIMIT $limit";
$t = $d->get($_q);


$ticketsData = array_map(function ($a) {
	$ticket = new Ticket((int)$a['TicketId']);
	return $ticket->toJsonObject();
}, $t);

jsonHeader();
echo json_encode($ticketsData);
