<?php
require_once __DIR__ . '/src/bootstrap.php';
login::requireIsUser();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

if (login::isAgent()) {

	$s->assign("tickets", TicketList::getLatestOpenTickets());
	$s->assign("title", "Dashboard");
	$s->assign("content", "agent_dashboard");

} else {
	$user = login::getUser();
	$ouser = $user->getOrganizationUser();
	if (!$ouser)
		die("We found an error. (13)");

	$tas = $ouser->getTicketAssociates();
	$tickets = array_map(fn($ta) => $ta->getTicket(), $tas);
	$tickets = array_filter($tickets, fn($t) => $t->isValid());
	$s->assign("tickets", array_reverse($tickets));
	$openTickets = array_filter($tickets, fn($t) => $t->getStatus()->isOpen() === true);
	$s->assign("openTickets", $openTickets);

	$s->assign("user", $user);
	$s->assign("ouser", $ouser);
	$s->assign("title", "My Tickets");
	$s->assign("content", "user_dashboard");
}


$s->display('__master.tpl');
