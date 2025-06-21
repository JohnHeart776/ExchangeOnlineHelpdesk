<?php

namespace Client;

use Auth\GraphCertificateAuthenticator;
use CurlHelper;
use Exception;
use Struct\GraphMail;
use Struct\GraphMailAttachment;
use Struct\GraphUser;
use Struct\GraphUserImage;

class GraphClient
{
	/**
	 * Base URL for Graph API.
	 *
	 * @var string
	 */
	private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';

	/**
	 * Authenticator that provides the certificate-based token.
	 *
	 * @var GraphCertificateAuthenticator
	 */
	private GraphCertificateAuthenticator $auth;

	public function __construct(GraphCertificateAuthenticator $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Ruft das Access-Token vom Authentifizierer ab.
	 *
	 * @return string
	 */
	private function getAccessToken(): string
	{
		return $this->auth->getAccessToken();
	}

	/**
	 * Führt eine GET-Anfrage aus und fügt die Standard-Header (inklusive Authorization) hinzu.
	 *
	 * @param string $url
	 * @param array  $additionalHeaders
	 * @return string
	 * @throws Exception
	 */
	private function getRequest(string $url, array $additionalHeaders = []): string
	{
		$accessToken = $this->getAccessToken();
		$headers = array_merge([
			"Authorization: Bearer {$accessToken}",
			"Accept: application/json",
		], $additionalHeaders);

		return CurlHelper::get($url, $headers);
	}

	/**
	 * Führt eine POST-Anfrage mit JSON-Payload aus.
	 *
	 * @param string $url
	 * @param array  $payload
	 * @param array  $additionalHeaders
	 * @return string
	 * @throws Exception
	 */
	private function postJsonRequest(string $url, array $payload, array $additionalHeaders = []): string
	{
		$accessToken = $this->getAccessToken();
		$headers = array_merge([
			"Authorization: Bearer {$accessToken}",
			"Content-Type: application/json",
		], $additionalHeaders);

		return CurlHelper::postJson($url, $payload, $headers);
	}

	/**
	 * Ruft Benutzerinformationen anhand der UPN ab.
	 *
	 * @param string $upn
	 * @return GraphUser|null
	 * @throws Exception
	 */
	public function getUserInfo(string $upn): ?GraphUser
	{
		$url = $this->graphEndpoint . '/users/' . rawurlencode($upn)
			. '?$select=id,displayName,userPrincipalName,mail,givenName,surname,jobTitle,department,companyName,mobilePhone,officeLocation,businessPhones,accountEnabled';

		try {
			$response = $this->getRequest($url);
		} catch (Exception $e) {
			if (str_contains($e->getMessage(), '(404)')) {
				return null; // Benutzer nicht gefunden
			}
			throw $e;
		}

		$data = json_decode($response, true);
		if (!isset($data['id'])) {
			return null;
		}

		return GraphUser::fromArray($data);
	}

	/**
	 * Ruft das Profilbild eines Benutzers ab.
	 *
	 * @param string $upn
	 * @return ?GraphUserImage
	 * @throws Exception
	 */
	public function getUserImage(string $upn): ?GraphUserImage
	{
		$accessToken = $this->getAccessToken();
		$url = $this->graphEndpoint . '/users/' . rawurlencode($upn) . '/photo/$value';

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				"Authorization: Bearer {$accessToken}",
			],
		]);
		$binaryData = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);

		if ($code === 404) {
			return null;
		}
		if (!$binaryData || $code >= 400) {
			throw new Exception("Profile image could not be loaded: ($code) $err");
		}

		return new GraphUserImage($binaryData);
	}

	/**
	 * Holt Daten von einer beliebigen Graph-API-Seite.
	 *
	 * @param string $url
	 * @return array
	 * @throws Exception
	 */
	public function fetchUserPage(string $url): array
	{
		$response = $this->getRequest($url);
		return json_decode($response, true);
	}

	/**
	 * Sendet eine E-Mail im Namen eines bestimmten Benutzers.
	 *
	 * @param string $upn          Absender (User Principal Name)
	 * @param string $subject      Betreff der E-Mail
	 * @param string $body         Inhalt der E-Mail (HTML erlaubt)
	 * @param array  $toRecipients Liste der Empfänger (als Array von E-Mail-Adressen)
	 * @return bool
	 * @throws Exception
	 */
	public function sendMailAsUser(string $upn, string $subject, string $body, array $toRecipients): bool
	{
		$url = $this->graphEndpoint . '/users/' . rawurlencode($upn) . '/sendMail';

		$recipients = array_map(function ($email) {
			return ['emailAddress' => ['address' => $email]];
		}, $toRecipients);

		$payload = [
			'message' => [
				'subject' => $subject,
				'body' => [
					'contentType' => 'HTML',
					'content' => $body,
				],
				'toRecipients' => $recipients,
			],
		];

		$this->postJsonRequest($url, $payload);
		return true;
	}

	/**
	 * Sendet eine E-Mail mit Anhängen und inline Bildern im Namen eines bestimmten Benutzers.
	 *
	 * @param string $upn          Absender (User Principal Name)
	 * @param string $subject      Betreff der E-Mail
	 * @param string $body         Inhalt der E-Mail (HTML erlaubt)
	 * @param array  $toRecipients Liste der Empfänger (als Array von E-Mail-Adressen)
	 * @param array  $attachments  Array von Anhängen. Jeder Anhang wird als assoziatives Array erwartet mit:
	 *                             - 'name'         => Dateiname
	 *                             - 'contentType'  => MIME-Typ (z.B. "text/plain" oder "image/jpeg")
	 *                             - 'contentBytes' => Der Dateiinhalts-String (Rohdaten, nicht base64-kodiert)
	 *                             Optional für inline Bilder:
	 *                             - 'isInline'     => true
	 *                             - 'contentId'    => Eine eindeutige ID, die im HTML-Body via cid: referenziert wird
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function sendMultipartMailAsUser(
		string $upn,
		string $subject,
		string $body,
		array  $toRecipients,
		array  $attachments = []
	): bool
	{
		$url = $this->graphEndpoint . '/users/' . rawurlencode($upn) . '/sendMail';

		$recipients = array_map(function ($email) {
			return ['emailAddress' => ['address' => $email]];
		}, $toRecipients);

		// Formatiere die Anhänge für das JSON-Payload
		$formattedAttachments = [];
		foreach ($attachments as $att) {
			$attachmentData = [
				'@odata.type' => "#microsoft.graph.fileAttachment",
				'name' => $att['name'],
				'contentType' => $att['contentType'],
				// Der Graph-API erwartet einen base64-kodierten String
				'contentBytes' => base64_encode($att['contentBytes']),
			];

			// Falls es sich um ein inline Bild handelt, setze die entsprechenden Flags
			if (isset($att['isInline']) && $att['isInline'] === true) {
				$attachmentData['isInline'] = true;
				if (isset($att['contentId'])) {
					$attachmentData['contentId'] = $att['contentId'];
				}
			}

			$formattedAttachments[] = $attachmentData;
		}

		// Baue das JSON-Payload zusammen
		$payload = [
			'message' => [
				'subject' => $subject,
				'body' => [
					'contentType' => 'HTML',
					'content' => $body,
				],
				'toRecipients' => $recipients,
				'attachments' => $formattedAttachments,
			],
		];

		$postResult = $this->postJsonRequest($url, $payload);
		return true;
	}

	/**
	 * Ruft E-Mails aus dem Posteingang eines Benutzers ab.
	 *
	 * @param string $userEmail
	 * @param int    $maxCount Maximale Anzahl der abzurufenden E-Mails
	 * @return \Struct\GraphMail[]
	 * @throws Exception
	 */
	public function fetchMails(string $userEmail, int $maxCount = 100): array
	{
		$userEmailEnc = urlencode($userEmail);
		$mailFields = [
			"id",
			"internetMessageId",
			"internetMessageHeaders",
			"subject",
			"from",
			"sender",
			"toRecipients",
			"ccRecipients",
			"bccRecipients",
			"body",
			"bodyPreview",
			"receivedDateTime",
			"sentDateTime",
			"hasAttachments",
			"conversationId",
			"importance",
		];

		$params = http_build_query([
			"\$select" => implode(",", $mailFields),
			"\$top" => $maxCount,
			"\$orderby" => "receivedDateTime desc",
		]);

		$url = $this->graphEndpoint . "/users/{$userEmailEnc}/mailFolders/Inbox/messages?$params";

		$response = $this->getRequest($url);
		$data = json_decode($response, true);

		if (!isset($data['value']) || !is_array($data['value'])) {
			throw new Exception("Unexpected format in fetchMails()");
		}

		$mails = [];
		foreach ($data['value'] as $item) {
			$mails[] = \Struct\GraphMail::fromGraphData($item, $userEmail);
		}
		return $mails;
	}

	/**
	 * Ruft alle Anhänge einer bestimmten E-Mail ab.
	 *
	 * @param string $userEmail Die E-Mail-Adresse des Benutzers (UPN)
	 * @param string $messageId Die Azure-ID der Nachricht
	 * @return GraphMailAttachment[]
	 * @throws Exception
	 */
	public function fetchAttachments(string $userEmail, string $messageId): array
	{
		$url = $this->graphEndpoint . '/users/' . urlencode($userEmail) . '/messages/' . urlencode($messageId) . '/attachments';
		$response = $this->getRequest($url);
		$data = json_decode($response, true);

		if (!isset($data['value'])) {
			throw new Exception("Invalid response format when retrieving attachments.");
		}

		$attachments = [];
		foreach ($data['value'] as $item) {
			$attachments[] = GraphMailAttachment::fromGraphData($item);
		}

		return $attachments;
	}

	/**
	 * Ruft eine spezifische E-Mail anhand ihrer Azure-ID ab.
	 *
	 * @param string $userEmail
	 * @param string $mailId
	 * @return array
	 * @throws Exception
	 */
	public function getMailFromAzureAsJsonObject(string $userEmail, string $mailId): array
	{
		$url = $this->graphEndpoint . '/users/' . urlencode($userEmail) . '/messages/' . urlencode($mailId);
		$response = $this->getRequest($url);
		return json_decode($response, true);
	}

	/**
	 * Ruft eine spezifische E-Mail anhand ihrer ID ab.
	 *
	 * @param string $mailbox Die E-Mail-Adresse bzw. Mailbox des Benutzers.
	 * @param string $mailId  Die eindeutige ID der E-Mail.
	 * @return \Struct\GraphMail
	 * @throws Exception Falls die E-Mail nicht gefunden wird.
	 */
	public function getMailFromAzureAsGraphMail(string $mailbox, string $mailId): \Struct\GraphMail
	{
		$mailData = $this->getMailFromAzureAsJsonObject($mailbox, $mailId);
		if (!isset($mailData['id'])) {
			throw new Exception("Email with ID {$mailId} not found.");
		}
		return \Struct\GraphMail::fromGraphData($mailData);
	}

	/**
	 * Ändert den Betreff einer E-Mail, indem ein Prefix hinzugefügt wird.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @param string $prefix  The prefix to be added.
	 * @throws Exception If the email was not found in the mailbox.
	 */
	public function prefixMailSubject(string $mailbox, string $mailId, string $prefix): void
	{
		// Ruft die E-Mail anhand ihrer ID ab
		$mail = $this->getMailFromAzureAsGraphMail($mailbox, $mailId);
		$currentSubject = $mail->subject;
		$newSubject = trim($prefix) . " " . trim($currentSubject);

		// Aktualisiert den Betreff der E-Mail
		$this->updateMailSubject($mailbox, $mail->id, $newSubject);
	}

	/**
	 * Ändert den Betreff einer E-Mail, indem ein Suffix hinzugefügt wird.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @param string $suffix  The suffix to be added.
	 * @throws Exception If the email was not found in the mailbox.
	 */
	public function suffixMailSubject(string $mailbox, string $mailId, string $suffix): void
	{
		// Ruft die E-Mail anhand ihrer ID ab
		$mail = $this->getMailFromAzureAsGraphMail($mailbox, $mailId);
		$currentSubject = $mail->subject;
		$newSubject = trim($currentSubject) . " " . trim($suffix);

		// Aktualisiert den Betreff der E-Mail
		$this->updateMailSubject($mailbox, $mail->id, $newSubject);
	}


	/**
	 * Aktualisiert den Betreff einer spezifischen E-Mail.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @param string $subject The new subject.
	 * @throws Exception If the update fails.
	 */
	public function updateMailSubject(string $mailbox, string $mailId, string $subject): void
	{
		$mailboxEnc = urlencode($mailbox);
		$mailIdEnc = urlencode($mailId);
		$url = $this->graphEndpoint . "/users/{$mailboxEnc}/messages/{$mailIdEnc}";

		$payload = ['subject' => $subject];

		$this->patchJsonRequest($url, $payload);
	}

	/**
	 * Führt eine PATCH-Anfrage mit JSON-Payload aus.
	 *
	 * @param string $url               The URL for the PATCH request.
	 * @param array  $payload           The data to be updated.
	 * @param array  $additionalHeaders Additional HTTP headers (optional).
	 * @return string
	 * @throws Exception If the request fails.
	 */
	private function patchJsonRequest(string $url, array $payload, array $additionalHeaders = []): string
	{
		$accessToken = $this->getAccessToken();
		$headers = array_merge([
			"Authorization: Bearer {$accessToken}",
			"Content-Type: application/json",
		], $additionalHeaders);

		return CurlHelper::patchJson($url, $payload, $headers);
	}

}
