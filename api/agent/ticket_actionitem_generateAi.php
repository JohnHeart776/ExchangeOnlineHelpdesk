<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket not found."));

$ticket = new Ticket($_POST["ticket"]);

$prompt = PromptHelper::generatePromptForTicketActionItems($ticket);
$content = AiService::getRepsonse($prompt);
$content = json_decode($content);
if (!$content)
	die(jsonStatus(false, "OpenAI response not valid."));

$r = [];
foreach ($content as $item) {
	$ticketActionItem = $ticket->addCustomTicketActionItem($item->title, $item->description);
	$r[] = $ticketActionItem->toJsonObject();
}

die(jsonStatus(true, "", $r));
