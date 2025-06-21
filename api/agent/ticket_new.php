<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Get default open status
$openStatus = TicketStatusHelper::getDefaultStatus();

// Validate input
$errors = [];
if (empty($_POST['subject'])) {
	$errors[] = 'Please enter a subject';
}
if (empty($_POST['text'])) {
	$errors[] = 'Please enter a description';
}
if (empty($_POST['category'])) {
	$errors[] = 'Please select a category';
}
if (!empty($errors)) {
	die(jsonStatus(
		false,
		implode("\n", $errors)
	));
}

// Validate and get category
$category = new Category((int)$_POST['category']);
if (!$category->isValid()) {
	die(jsonStatus(
		false,
		'Invalid category selected'
	));
}

// Validate and get owner if set
$owner = null;
if (!empty($_POST['owner'])) {
	$owner = new User((int)$_POST['owner']);
	if (!$owner->isValid()) {
		die(jsonStatus(
			false,
			'Invalid assignee selected'
		));
	}
}

if (!isset($_POST["reportee"]))
	die(jsonStatus(false, "Invalid Reportee 2"));
$reportee = new OrganizationUser((int)$_POST["reportee"]);
if (!$reportee->isValid())
	die(jsonStatus(false, "Invalid Reportee"));

// Create ticket
$ticket = new Ticket(0);
$ticket->Subject = $_POST['subject'];
$ticket->CategoryId = $category->getCategoryIdAsInt();
$ticket->StatusId = $openStatus->getStatusIdAsInt();
if ($owner) {
	$ticket->AssigneeUserId = $owner->getUserId();
}
$ticket->CreatedDatetime = date("Y-m-d H:i:s");
//$ticket->ConversationId = hash('sha256', uniqid(mt_rand(), true));
$ticket->Secret1 = TicketTooling::getTicketSecret(12);
$ticket->Secret2 = TicketTooling::getTicketSecret(16);
$ticket->Secret3 = TicketTooling::getTicketSecret(24);
$ticket->DueDatetime = SlaHelper::getSlaDueDate()->format("Y-m-d H:i:s"); //set initial due value from now
$ticket->TicketNumber = TicketNumberHelper::getNextTicketNumber();
$ticket->MessengerName = $reportee->getDisplayName();
$ticket->MessengerEmail = $reportee->getMail();

$newTicket = TicketController::save($ticket);


// add the text as the first ticket comment
$newTicketComment = $newTicket->addTicketComment(
	text: $_POST['text'],
	user: login::getUser(),
	facility: EnumTicketCommentFacility::user,
	textType: EnumTicketCommentTextType::html,
	accessLevel: EnumTicketCommentAccessLevel::Public
);

//add reportee as associate
$newTicket->addTicketAssociate($reportee);

if ($newTicket->isValid()) {
	if (!empty($_POST['assignees']) && is_array($_POST['assignees'])) {
		foreach ($_POST['assignees'] as $assigneeId) {
			$assignee = new OrganizationUser((int)$assigneeId);
			if ($assignee->isValid()) {
				$ticket->addTicketAssociate($assignee);
			}
		}
	}

	die(
	jsonStatus(
		true,
		'Ticket was created successfully',
		['link' => '/ticket/' . $ticket->getTicketNumber(), 'ticket' => $ticket->toJsonObject()]
	));
}

die(jsonStatus(
	false,
	'Ticket could not be created'
));
