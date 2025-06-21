<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Login::requireIsAgent();

if (empty($_POST['guid'])) {
	die(jsonStatus(false, 'Associate GUID is required'));
}

$guid = $_POST['guid'];
$associate = new TicketAssociate($guid);

if (!$associate) {
	die(jsonStatus(false, 'Associate not found'));
}

$ticket = $associate->getTicket();
$ouser = $associate->getOrganizationUser();

if ($associate->delete()) {
	$ticket->addTicketComment(
		text: 'Associated user ' . $ouser->getDisplayName() . ' has been removed.',
		user: login::getUser(),
		facility: EnumTicketCommentFacility::user,
		accessLevel: EnumTicketCommentAccessLevel::Public,
	);
	die(jsonStatus());
}

die(jsonStatus(false, "Unknown Error"));


