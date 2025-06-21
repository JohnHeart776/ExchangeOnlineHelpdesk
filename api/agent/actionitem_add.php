<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['actiongroup'], $_POST['name'], $_POST['position'])) {
	echo jsonStatus(false);
}

try {
	// Get the action group
	$actionGroup = new ActionGroup($_POST['actiongroup']);

	// Create new action item
	$actionItem = new ActionItem(0);
	$actionItem->Title = $_POST['name'];
	$actionItem->Description = "";
	$actionItem->ActionGroupId = $actionGroup->getActionGroupId();

	// Set position
	if ($_POST['position'] === 'end') {
		$actionItem->SortOrder = $actionGroup->getNextSortOrder();
	}

	// Save the action item
	$newActionItem = ActionItemController::save($actionItem);

	echo jsonStatus(true, "", ["actionitem" => $newActionItem->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false);
}

