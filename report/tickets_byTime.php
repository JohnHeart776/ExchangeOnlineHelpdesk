<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if ($start) {
	$startDate = DateTime::createFromFormat('Y-m-d', $start);
} else {
	$startDate = new DateTime("-3 months");
}

if (!$startDate) {
	throw new Exception("Invalid start date format. Use YYYY-MM-DD");
}
$startDate->setTime(0, 0, 0);

if ($end) {
	$endDate = DateTime::createFromFormat('Y-m-d', $end);
	if (!$endDate) {
		throw new Exception("Invalid end date format. Use YYYY-MM-DD");
	}
} else {
	$endDate = (clone $startDate)->modify('+3 months')->modify('-1 second');
}

$interval = $startDate->diff($endDate);
if ($interval->y > 0 || $interval->m > 12) {
	throw new Exception("Maximum time frame is 12 months");
}

$report = new ReportingTicketsByTime($startDate, $endDate);
$s->assign("report", $report);
$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Tickets by Day Report");
$s->assign("content", "report_tickets_byDay");
$s->display('__master.tpl');
