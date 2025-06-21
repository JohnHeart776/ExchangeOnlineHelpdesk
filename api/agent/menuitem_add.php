<?php
require_once __DIR__ . '/../../src/bootstrap.php';

// Ensure user is logged in as agent
Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['title'], $_POST['link'], $_POST['menu'], $_POST["parent"])) {
	echo jsonStatus(false, "Missing required parameters");
}

$parent = null;
if (strlen($_POST["parent"]) > 0) {
	$parent = new MenuItem($_POST["parent"]);
}
if (!$parent?->isValid())
	$parent = null;

try {
	// Get the menu
	$menu = new Menu($_POST['menu']);
	if (!$menu->isValid())
		throw new Exception("Menu not found");

	// Create new menu item
	$menuItem = new MenuItem(0);
	$menuItem->Title = $_POST['title'];
	$menuItem->Link = $_POST['link'];
	$menuItem->MenuId = $menu->getMenuId();

	if ($parent)
		$menuItem->ParentMenuItemId = $parent->getMenuItemId();


	$menuItem->SortOrder = $menu->getNextSortOrder();


	// Save the menu item
	$newMenuItem = MenuItemController::save($menuItem);

	echo jsonStatus(true, "", ["menuitem" => $newMenuItem->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false);
}

