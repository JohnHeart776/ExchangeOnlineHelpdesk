<?php

class TicketHelper
{


	public static function createNewTicketFromGraphMail(\Struct\GraphMail $graphMail, \Mail $mail): ?Ticket
	{
		$newTicket = self::createEmptyUnsavedTicket();
		$newTicket->ConversationId = $graphMail->conversation_id;
		$newTicket->MessengerName = $graphMail->from_name;
		$newTicket->MessengerEmail = $graphMail->from_email;
		$newTicket->Subject = strip_tags($mail->Subject) ?? '';

		$ticket = TicketController::save($newTicket);
		$mail->update("TicketId", $ticket->TicketId);

		return $ticket;
	}

	public static function createEmptyUnsavedTicket(): Ticket
	{
		$newTicketEnvelope = new Ticket(0);
		$newTicketEnvelope->Secret1 = TicketTooling::getTicketSecret(12);
		$newTicketEnvelope->Secret2 = TicketTooling::getTicketSecret(16);
		$newTicketEnvelope->Secret3 = TicketTooling::getTicketSecret(24);
		$newTicketEnvelope->DueDatetime = SlaHelper::getSlaDueDate()->format("Y-m-d H:i:s"); //set initial due value from now
		$newTicketEnvelope->TicketNumber = TicketNumberHelper::getNextTicketNumber();
		$newTicketEnvelope->CategoryId = CategoryHelper::getDefaultId() ?? 1;
		$newTicketEnvelope->AssigneeUserId = null;
		$newTicketEnvelope->StatusId = null;

		return $newTicketEnvelope;
	}

	public static function stringContainsATicketMarker(?string $subject): bool
	{
		return preg_match("/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/", $subject, $matches) > 0;
	}

	public static function removeTicketMarkerFromString(string $subject): string
	{
		return preg_replace("/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/", "", $subject);
	}

	public static function extractTicketMarkerFromString(string $string): ?string
	{
		preg_match("/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/", $string, $matches);
		return $matches[0] ?? null;
	}

	public static function extractTicketNumberFromString(string $string): ?string
	{
		preg_match("/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/", $string, $matches);
		return $matches[1] ?? null;
	}
}
