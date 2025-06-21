<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['menuitem'])) {
	echo jsonStatus(false);
}

try {
	// Delete menu item
	$menuItem = new MenuItem($_POST['menuitem']);
	if ($menuItem->delete()) {
		echo jsonStatus(true);
	} else {
		echo jsonStatus(false);
	}
} catch (Exception $e) {
	echo jsonStatus(false);
}


