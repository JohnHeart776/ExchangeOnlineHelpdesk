<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Organistaionsbenutzer");
$s->assign("content", "agent_organizationusers");
$s->display('__master.tpl');
