<?php
require_once __DIR__ . '/src/bootstrap.php';
login::requireIsUser();
global $s;

if (!isset($_GET["TicketNumber"]))
	header("Location: index.php");

$ticket = TicketController::searchOneBy("TicketNumber", $_GET["TicketNumber"]);
if (!$ticket?->isValid())
	header("Location: index.php");
$s->assign("ticket", $ticket);

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);


$s->assign("title", $ticket->getTicketNumber() . " - " . $ticket->getSubjectForMailSubject(false));

if (login::isAgent()) {
	$s->assign("content", "agent_ticket");
} else {

	$myOUser = login::getUser()->getOrganizationUser();
	if (!$myOUser)
		die("We found an error but we don't want to keep it. (0x13)");

	//check if i am in the ticket associates
	$amILinked = false;
	foreach ($ticket->getTicketAssociates() as $ta)
		if ($ta->getOrganizationUser()->equals($myOUser))
			$amILinked = true;

	if (!$amILinked)
		die("You don't have permission to access this ticket.");

	$ticketFiles = [];
	if ($ticket->hasTicketFiles()) {
		foreach ($ticket->getTicketFiles() as $ticketFile) {
			if ($ticketFile->isAccessLevelPublic())
				$ticketFiles[] = $ticketFile;
		}
	}
	$s->assign("ticketFiles", $ticketFiles);

	$s->assign("content", "user_ticket");
}

$s->display('__master.tpl');
