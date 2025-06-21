<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Neues Ticket erstellen");
$s->assign("content", "agent_ticket_new");
$s->display('__master.tpl');
