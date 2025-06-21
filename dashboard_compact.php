<?php
require_once __DIR__ . '/src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$view = $_GET['view'] ?? 'open';
$tickets = match ($view) {
	'all' => TicketList::getAllTickets(),
	'open' => TicketList::getLatestOpenTickets(),
	'due' => TicketList::getDueTickets(),
};
$dashboardTitle = match ($view) {
	'all' => "Alle " . count($tickets) . " Tickets",
	'open' => "Offene " . count($tickets) . " Tickets",
	'due' => "Alle " . count($tickets) . " fälligen Tickets",
};

$s->assign("tickets", $tickets);
$s->assign("dashboardTitle", $dashboardTitle);

$s->assign("title", "Dashboard Kompakt");
$s->assign("content", "agent_dashboard_compact");
$s->display('__master.tpl');
