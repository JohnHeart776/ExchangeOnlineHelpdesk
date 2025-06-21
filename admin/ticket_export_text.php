<?php
require_once __DIR__ . '/../src/bootstrap.php';
login::requireIsAdmin();

if (!isset($_GET['guid']) || empty($_GET['guid'])) {
	die('Missing ticket GUID');
}

$ticket = new Ticket($_GET['guid']);
if (!$ticket->isValid()) {
	die('Ticket not found');
}

header('Content-Type: text/plain');

echo $ticket->extractAllText();
