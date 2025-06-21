<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Benachrichtigungstemplates");
$s->assign("content", "admin_notificationtemplates");
$s->display('__master.tpl');
