<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAgent();
global $s;

$menu = MenuController::searchOneBy("Name", "ApplicationMenu");
$s->assign("menu", $menu);

$suggestions = CategorySuggestionController::getAll(0, "DESC", "Priority");
usort($suggestions, function ($a, $b) {
	if ($a->Priority !== $b->Priority) {
		return $b->Priority <=> $a->Priority;
	}
	return strlen($b->Filter) <=> strlen($a->Filter);
});

$s->assign("suggestions", $suggestions);

$s->assign("title", "Kategorie-Empfehlungen");
$s->assign("content", "agent_categorysuggestions");
$s->display('__master.tpl');
