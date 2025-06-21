<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

if (!isset($_GET['guid'])) {
	throw new Exception("Missing required parameter: guid");
}

$user = new User($_GET["guid"]);
if (!$user->isValid())
	throw new Exception("Invalid user");

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("user", $user);

$s->assign("title", $user->getDisplayName() . " | Benutzer");
$s->assign("content", "admin_user");
$s->display('__master.tpl');
