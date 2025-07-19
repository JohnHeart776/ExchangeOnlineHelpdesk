<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["ticket"]))
	die(jsonStatus(false, "Ticket not found."));

$ticket = new Ticket($_POST["ticket"]);

if (!isset($_POST["ouser"]))
	die(jsonStatus(false, "OUser not found."));
$ouser = new OrganizationUser($_POST["ouser"]);

if ($ticket->hasAssociatedOrganizationUser($ouser))
	die(jsonStatus(true, "Associate already exists", $ticket->getTicketAssociateFromOrganizationUser($ouser)->toJsonObject()));


$associate = $ticket->addTicketAssociate(
	ouser: $ouser,
	sendMail: true,
);
die(jsonStatus(true, "", $associate->toJsonObject()));
