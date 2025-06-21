<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();
global $s;

if (!isset($_GET["guid"]))
	throw new Exception("No guid provided");

$category = CategoryController::searchOneBy("Guid", $_GET["guid"]);
if (!$category)
	throw new Exception("No category found");


$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$s->assign("category", $category);

$s->assign("title", "Kategorie " . $category->getPublicName());
$s->assign("content", "admin_category");
$s->display('__master.tpl');
