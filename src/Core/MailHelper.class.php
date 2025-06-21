<?php

class MailHelper
{

	/**
	 * @param \Client\GraphClient $client
	 * @param string              $userUpn
	 * @param array               $recipients
	 * @param string              $subject
	 * @param string              $body
	 * @param array               $attachments
	 * @return true
	 * @throws Exception
	 */
	public static function sendMultipartMail(
		\Client\GraphClient $client,
		string              $userUpn,
		array               $recipients,
		string              $subject,
		string              $body,
		array               $attachments = []
	): true
	{

		$client->sendMultipartMailAsUser(
			$userUpn,
			$subject,
			$body,
			$recipients,
			$attachments
		);

		return true;

	}

	/**
	 * @param \Client\GraphClient|null $client
	 * @param string                   $recipient
	 * @param string                   $subject
	 * @param string                   $mailInlineContentAsHtml
	 * @param array                    $attachments
	 * @return true
	 * @throws \Database\DatabaseQueryException
	 */
	public static function sendStyledMailFromSystemAccount(
		?\Client\GraphClient $client = null,
		string               $recipient,
		string               $subject,
		string               $mailInlineContentAsHtml,
		array                $attachments = []): true
	{
		if (!$client)
			$client = GraphHelper::getApplicationAuthenticatedGraph();

		$userUpn = Config::getConfigValueFor("source.mailbox");
		$html = static::getStyledMailTemplate();
		$html = str_replace("{{mailContent}}", $mailInlineContentAsHtml, $html);

		//log this Mail
		$LogMailSent = new LogMailSent(0);
		$LogMailSent->Recipient = $recipient;
		$LogMailSent->Body = $html;
		$LogMailSent->Subject = $subject;
		if (login::isLoggedIn())
			$LogMailSent->UserId = login::getUser()->getUserId();
		LogMailSentController::save($LogMailSent);

		//in case of debugging, route mails to somewhere else
		if (config::getConfigValueFor("debug.mails.route.all.enabled")) {
			$recipient = config::getConfigValueFor("debug.mails.route.all.to");
		}

		return static::sendMultipartMail($client, $userUpn, [$recipient], $subject, $html, $attachments);

	}

	public static function getStyledMailTemplate(): string
	{

		$html = Config::getConfigValueFor("mail.template");
		$html = str_replace("{{mail.logo.data}}", Config::getConfigValueFor("mail.logo.data"), $html);
		$html = str_replace("{{mail.logo.height}}", Config::getConfigValueFor("mail.logo.height"), $html);

		return $html;
	}

	public static function renderMailText(
		string            $text,
		?User             $user = null,
		?OrganizationUser $organizationUser = null,
		?Ticket           $ticket = null)
	{

		//universal replace function

		$text = str_replace("{{dateTime}}", date("d.m, H:i"), $text);
		$text = str_replace("{{date}}", date("d.m.Y"), $text);
		$text = str_replace("{{time}}", date("H:i"), $text);


		if ($user) {
			$text = str_replace("{{givenName}}", $user->getName(), $text);
			$text = str_replace("{{name}}", $user->getName(), $text);

			$text = str_replace("{{surname}}", $user->getSurname(), $text);

			$text = str_replace("{{fullName}}", $user->getDisplayName(), $text);
			$text = str_replace("{{displayName}}", $user->getDisplayName(), $text);

		}

		if ($organizationUser) {
			$text = str_replace("{{givenName}}", $organizationUser->getGivenName(), $text);
			$text = str_replace("{{name}}", $organizationUser->getGivenName(), $text);

			$text = str_replace("{{surname}}", $organizationUser->getSurname(), $text);

			$text = str_replace("{{displayName}}", $organizationUser->getDisplayName(), $text);
			$text = str_replace("{{fullName}}", $organizationUser->getDisplayName(), $text);

		}

		if ($ticket) {
			$text = str_replace("{{ticketNumber}}", $ticket->getTicketNumber(), $text);
			$text = str_replace("{{ticketMailSubject}}", $ticket->getSubjectForMailSubject(), $text);
			$text = str_replace("{{ticketSubject}}", $ticket->getSubject(), $text);
			$text = str_replace("{{ticketMarker}}", $ticket->getTicketMarkerForMailSubject(), $text);
			$text = str_replace("{{ticketLink}}", $ticket->getLink(), $text);
			$text = str_replace("{{ticketLinkAbsoluteInA}}", "<a href='{$ticket->getAbsoluteLink()}'>Link</a>", $text);
			$text = str_replace("{{ticketLinkAbsolute}}", $ticket->getAbsoluteLink(), $text);
		}

		$text = str_replace("{{finish}}", "Beste Grüße, dein NV IT-Service.", $text);

		return $text;


	}

}
