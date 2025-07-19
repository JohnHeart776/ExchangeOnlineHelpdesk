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
	 * Retrieves the access token from the authenticator.
	 *
	 * @return string
	 */
	private function getAccessToken(): string
	{
		return $this->auth->getAccessToken();
	}

	/**
	 * Executes a GET request and adds standard headers (including Authorization).
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
	 * Executes a POST request with JSON payload.
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
	 * Retrieves user information based on the UPN.
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
				return null; // User not found
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
	 * Retrieves the profile image of a user.
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
	 * Fetches data from any Graph API page.
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
	 * Sends an email on behalf of a specific user.
	 *
	 * @param string $upn          Sender (User Principal Name)
	 * @param string $subject      Email subject
	 * @param string $body         Email content (HTML allowed)
	 * @param array  $toRecipients List of recipients (as array of email addresses)
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
	 * Sends an email with attachments and inline images on behalf of a specific user.
	 *
	 * @param string $upn          Sender (User Principal Name)
	 * @param string $subject      Email subject
	 * @param string $body         Email content (HTML allowed)
	 * @param array  $toRecipients List of recipients (as array of email addresses)
	 * @param array  $attachments  Array of attachments. Each attachment is expected as associative array with:
	 *                             - 'name'         => Filename
	 *                             - 'contentType'  => MIME type (e.g. "text/plain" or "image/jpeg")
	 *                             - 'contentBytes' => The file content string (raw data, not base64 encoded)
	 *                             Optional for inline images:
	 *                             - 'isInline'     => true
	 *                             - 'contentId'    => A unique ID that is referenced in HTML body via cid:
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

		// Format the attachments for the JSON payload
		$formattedAttachments = [];
		foreach ($attachments as $att) {
			$attachmentData = [
				'@odata.type' => "#microsoft.graph.fileAttachment",
				'name' => $att['name'],
				'contentType' => $att['contentType'],
				// The Graph API expects a base64 encoded string
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
	 * Fetches emails from a user's inbox.
	 *
	 * @param string $userEmail
	 * @param int    $maxCount Maximum number of emails to fetch
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
	 * Fetches all attachments of a specific email.
	 *
	 * @param string $userEmail The email address of the user (UPN)
	 * @param string $messageId The Azure ID of the message
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
	 * Retrieves a specific email by its Azure ID.
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
	 * Retrieves a specific email by its ID.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @return \Struct\GraphMail
	 * @throws Exception If the email is not found.
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
	 * Updates an email's subject by adding a prefix.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @param string $prefix  The prefix to be added.
	 * @throws Exception If the email was not found in the mailbox.
	 */
	public function prefixMailSubject(string $mailbox, string $mailId, string $prefix): void
	{
		// Retrieve the email by its ID
		$mail = $this->getMailFromAzureAsGraphMail($mailbox, $mailId);
		$currentSubject = $mail->subject;
		$newSubject = trim($prefix) . " " . trim($currentSubject);

		// Update the email subject
		$this->updateMailSubject($mailbox, $mail->id, $newSubject);
	}

	/**
	 * Updates an email's subject by adding a suffix.
	 *
	 * @param string $mailbox The email address or mailbox of the user.
	 * @param string $mailId  The unique ID of the email.
	 * @param string $suffix  The suffix to be added.
	 * @throws Exception If the email was not found in the mailbox.
	 */
	public function suffixMailSubject(string $mailbox, string $mailId, string $suffix): void
	{
		// Retrieve the email by its ID
		$mail = $this->getMailFromAzureAsGraphMail($mailbox, $mailId);
		$currentSubject = $mail->subject;
		$newSubject = trim($currentSubject) . " " . trim($suffix);

		// Update the email subject
		$this->updateMailSubject($mailbox, $mail->id, $newSubject);
	}


	/**
	 * Updates the subject of a specific email.
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
	 * Executes a PATCH request with JSON payload.
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
