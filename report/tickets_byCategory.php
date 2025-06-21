<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

global $d;

$_q = "SELECT MIN(CreatedDatetime) as oldest FROM Ticket";
$result = $d->getPDO($_q);
$startDate = new DateTime($result[0]['oldest']);
$endDate = new DateTime();

$weeklyStats = [];
$currentDate = clone $startDate;

while ($currentDate <= $endDate) {
	$report = new ReportingTicketsByCategory($currentDate);
	$weeklyStats[] = [
		'week' => clone $currentDate,
		'stats' => $report->getCategoryStats(),
	];
	$currentDate->modify('+1 week');
}
$s->assign("report", $weeklyStats);
$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Tickets by Category Report");
$s->assign("content", "report_tickets_byCategory");
$s->display('__master.tpl');
