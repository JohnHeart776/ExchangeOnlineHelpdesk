<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Artikel");
$s->assign("content", "agent_articles");
$s->display('__master.tpl');
