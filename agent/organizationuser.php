<?php
require_once __DIR__ . '/../src/bootstrap.php';
Login::requireIsAgent();
global $s;

if (!isset($_GET['guid'])) {
	throw new Exception("Missing required parameter: guid");
}

$organizationUser = new OrganizationUser($_GET['guid']);
if (!$organizationUser->isValid()) {
	throw new Exception("Invalid organization user");
}
$s->assign("organizationUser", $organizationUser);


$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("title", "Organisationsbenutzer");
$s->assign("content", "agent_organizationuser");
$s->display('__master.tpl');
