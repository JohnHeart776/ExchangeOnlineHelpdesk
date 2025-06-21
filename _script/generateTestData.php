<?php
require_once __DIR__ . '/../src/bootstrap.php';

$startDate = new DateTime();
$endDate = (clone $startDate)->modify('-3 months');

$currentDate = clone $startDate;

while ($currentDate >= $endDate) {
	$timestamp = $currentDate->format('Y-m-d H:i:s');
	echo "[{$timestamp}] Processing date: " . $currentDate->format('Y-m-d') . PHP_EOL;

	$randomCloseChance = rand(0, 100);
	$numTickets = rand(1, 50);
	//if currentDate is a Sunday, set amount to rand 0..10, if saturday set to 0..8
	if ($currentDate->format('N') == 7)
		$numTickets = rand(0, 10);
	if ($currentDate->format("N") == 6)
		$numTickets = rand(0, 8);
	echo "[{$timestamp}] Generating {$numTickets} tickets..." . PHP_EOL;

	for ($i = 0; $i < $numTickets; $i++) {

		$orgUser = OrganizationUserController::getRandom(1)[0];

		$randomCategory = CategoryController::getRandom(1)[0];

		$newTicketEnvelope = TicketHelper::createEmptyUnsavedTicket();
		$newTicketEnvelope->Subject = "Support Ticket - " . $currentDate->format('Y-m-d') . " - " . uniqid();
		$newTicketEnvelope->StatusId = TicketStatusHelper::getDefaultStatusId();
		$ticketDateTime = (clone $currentDate)->setTime(0, 0)->setTime(rand(6, 21), rand(0, 59));
		$newTicketEnvelope->CreatedDatetime = $ticketDateTime->format('Y-m-d H:i:s');
		$newTicketEnvelope->ConversationId = base64_encode(openssl_random_pseudo_bytes(16));
		$newTicketEnvelope->MessengerEmail = $orgUser->getMail();
		$newTicketEnvelope->MessengerName = $orgUser->getMail();
		$newTicketEnvelope->DueDatetime = SlaHelper::getSlaDueDate($ticketDateTime)->format("Y-m-d H:i:s");
		$newTicketEnvelope->CategoryId = $randomCategory->CategoryId;
		$ticket = TicketController::save($newTicketEnvelope);


		$ticket->addTicketComment(
			text: "This is a test ticket. Please ignore. " . uniqid(),
			facility: EnumTicketCommentFacility::system,
			textType: EnumTicketCommentTextType::txt,
			accessLevel: EnumTicketCommentAccessLevel::Public,
		);

		$ticket->addTicketAssociate($orgUser, false);

		//by chance of 66% close the ticket
		if (rand(0, 100) < $randomCloseChance) {
			$ticket->close();
			echo "\tClosed..." . PHP_EOL;
		}

		echo "[{$timestamp}] Created ticket #{$ticket->TicketNumber}" . PHP_EOL;
	}

	$currentDate->modify('-1 day');
}

echo "[" . (new DateTime())->format('Y-m-d H:i:s') . "] Script completed" . PHP_EOL;

