<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

if (!isset($_GET['guid'])) {
	throw new Exception("Missing required parameter: guid");
}

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$menuItem = new MenuItem($_GET['guid']);
if (!$menuItem->isValid())
	throw new Exception("Invalid menu item");
$s->assign("menuItem", $menuItem);

$s->assign("title", "Menüeintrag");
$s->assign("content", "agent_menuitem");
$s->display('__master.tpl');
