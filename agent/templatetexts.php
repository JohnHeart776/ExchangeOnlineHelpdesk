<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Template Texte");
$s->assign("content", "agent_templatetexts");
$s->display('__master.tpl');
