<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Check if ticket GUID is provided
if (!isset($_POST['guid']) || empty($_POST['guid'])) {
	die(jsonStatus(false, "No ticket GUID provided"));
}

// Check if AI is enabled in config
if (!Config::getConfigValueFor('ai.enable')) {
	die(jsonStatus(true, "ich kann keinen text generieren"));
}

try {
	// Get ticket by GUID
	$ticket = new Ticket($_POST['guid']);
	if (!$ticket) {
		die(jsonStatus(false, "Ticket not found"));
	}

	$recap = $ticket->getRecapWithAi();

	echo jsonStatus(true, "", ['text' => $recap]);
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}

