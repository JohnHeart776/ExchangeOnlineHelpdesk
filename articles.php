<?php
require_once __DIR__ . '/src/bootstrap.php';
login::requireIsUser();

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
global $s;
$s->assign("menu", $menu);

$articles = ArticleController::getAll(sortBy: "Title");
$s->assign("articles", $articles);
$s->assign("title", "Artikel");
$s->assign("content", "articles");
$s->display('__master.tpl');
