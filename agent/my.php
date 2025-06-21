<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

//$agent = login::getUser()->getAgentWrapper();
//$calendar = $agent->getTicketCalendarDays(new DateTime("-7 days"), new DateTime("+3 days"));
$s->assign("calendar", new CalendarRange(new DateTime("-7 days"), new DateTime("+3 days")));

$s->assign("title", "Mein Bereich");
$s->assign("content", "agent_my");
$s->display('__master.tpl');
