<?php

class TicketTooling
{

	public static function getTicketSecret(int $length = 12): string
	{

		$guid = bin2hex(random_bytes(16)); // Generate a random GUID
		$uniqueId = uniqid("", true);      // Create a unique ID
		$hash = sha1($guid . $uniqueId);   // Create a SHA1 hash of the concatenated values
		return substr($hash, 0, $length);  // Return the secret with the specified length
	}

	/**
	 * @param \Struct\GraphMail $graphMail
	 * @param Ticket            $Ticket
	 * @return TicketComment
	 */
	public static function convertGraphMailToUnsavedTicketComment(
		\Struct\GraphMail $graphMail,
		Ticket            $Ticket
	): TicketComment
	{
		$newTicketComment = new TicketComment(0);
		$newTicketComment->TicketId = $Ticket->TicketId;
		$newTicketComment->Facility = 'user';
		$newTicketComment->Text = TicketTextCleaner::clean($graphMail->body);
		$newTicketComment->GraphObject = $graphMail->toJson();
		return $newTicketComment;
	}


}
