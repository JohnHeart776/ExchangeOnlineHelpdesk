<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

if (!isset($_GET['guid'])) {
	throw new Exception("Missing required parameter: guid");
}
$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$article = new Article($_GET['guid']);
$s->assign("article", $article);

$s->assign("title", $article->getTitle() . " - Article");
$s->assign("content", "agent_article");
$s->display('__master.tpl');
