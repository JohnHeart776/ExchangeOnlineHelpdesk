<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"])) {
	die(jsonStatus(false, "Ticket ID is required."));
}

if (!isset($_POST["agent"])) {
	die(jsonStatus(false, "Agent ID is required."));
}

$agent = new User($_POST["agent"]);
if (!$agent->isValid()) {
	die(jsonStatus(false, "Invalid agent selected."));
}

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

$ticket = $ticket->assignUser($agent);

$defaultAssignedStatus = TicketStatusHelper::getDefaultAssignedStatus();
//when assigning a ticket to an agent, always set the status to assigned

$sendAgentNotification = !$agent->equals(login::getUser());
$ticket->setStatus(
	status: $defaultAssignedStatus,
	sendAgentNotification: $sendAgentNotification,
	agentUser: $agent,
);


echo jsonStatus(true, "", $ticket->toJsonObject());
