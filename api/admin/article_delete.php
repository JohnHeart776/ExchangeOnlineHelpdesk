<?php
require_once __DIR__ . '/../../src/bootstrap.php';

Login::requireIsAdmin();

// Validate required parameters
if (!isset($_POST['guid'])) {
	echo jsonStatus(false);
}

try {
	$article = new Article($_POST['guid']);
	if (!$article->isValid())
		die(jsonStatus(false, "Article not found"));

	if ($article->delete()) {
		echo jsonStatus(true);
	} else {
		echo jsonStatus(false);
	}
} catch (Exception $e) {
	echo jsonStatus(false);
}


