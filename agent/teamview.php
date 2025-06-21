<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$agents = array_filter(UserController::getAll(), fn($user) => $user->isAgent());
$s->assign("agents", $agents);

$s->assign("title", "Team-Ansicht");
$s->assign("content", "agent_teamview");
$s->display('__master.tpl');
