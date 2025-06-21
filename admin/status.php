<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$status = new Status($_GET['guid']);
if (!$status->isValid())
	throw new Exception("Invalid status");
$s->assign("status", $status);

$s->assign("title", "Status");
$s->assign("content", "admin_status");
$s->display('__master.tpl');
