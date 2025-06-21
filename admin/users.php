<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Benutzer");
$s->assign("content", "admin_users");
$s->display('__master.tpl');
