<?php
require_once __DIR__ . '/src/bootstrap.php';
login::requireIsUser();
global $s;

if (!isset($_GET["slug"]))
	throw new Exception("Missing required parameter: slug");

$article = Article::bySlug($_GET["slug"]);
if (!$article->isValid())
	throw new Exception("Invalid article");

if ($article->getAccessLevelIsAgent() && !login::getUser()->isAgent())
	die("Invalid Articles Access Level");

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("article", $article);
$s->assign("title", $article->getTitle());
$s->assign("content", "article");
$s->display('__master.tpl');
