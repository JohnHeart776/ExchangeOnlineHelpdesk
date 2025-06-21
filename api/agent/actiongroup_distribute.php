<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['actiongroup'])) {
	echo jsonStatus(false);
}

try {
	$actionGroup = new ActionGroup($_POST['actiongroup']);
	foreach ($actionGroup->getActionItems() as $k => $actionItem) {
		$actionItem->update("SortOrder", ($k + 1) * 10);
	}
	echo jsonStatus(true);
} catch (Exception $e) {
	echo jsonStatus(false);
}
