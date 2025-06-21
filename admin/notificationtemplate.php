<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

if (!isset($_GET["guid"]))
	throw new Exception("No guid provided");

$template = NotificationTemplateController::searchOneBy("Guid", $_GET["guid"]);
if (!$template)
	throw new Exception("No template found");


$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("template", $template);

$s->assign("title", "Benachrichtigungstemplate: " . $template->getName());
$s->assign("content", "admin_notificationtemplate");
$s->display('__master.tpl');
