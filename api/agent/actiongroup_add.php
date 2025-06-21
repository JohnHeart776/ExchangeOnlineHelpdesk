<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['name'], $_POST['position'])) {
	echo jsonStatus(false);
}

try {
	// Create new action group
	$actionGroup = new ActionGroup(0);
	$actionGroup->Name = $_POST['name'];

	// Set position
	if ($_POST['position'] === 'end') {
		$actionGroup->SortOrder = ActionGroup::getGlobalNextSortOrder();
	}

	// Save the action group
	$newActionGroup = ActionGroupController::save($actionGroup);

	echo jsonStatus(true, "", ["actiongroup" => $newActionGroup->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false);
}
