<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

if (!isset($_GET['guid'])) {
	throw new Exception("Missing required parameter: guid");
}
$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$templatetext = new TemplateText($_GET['guid']);
$s->assign("templateText", $templatetext);

$s->assign("title", "Template Text");
$s->assign("content", "agent_templatetext");
$s->display('__master.tpl');
