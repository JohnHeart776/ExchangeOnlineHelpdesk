<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['actionitem'])) {
	echo jsonStatus(false);
}

try {
	// Delete action item
	$actionItem = new ActionItem($_POST['actionitem']);
	if ($actionItem->delete()) {
		echo jsonStatus(true);
	} else {
		echo jsonStatus(false);
	}
} catch (Exception $e) {
	echo jsonStatus(false);
}


