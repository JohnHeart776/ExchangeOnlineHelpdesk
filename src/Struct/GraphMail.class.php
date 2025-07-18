<?php

namespace Struct;

class GraphMail
{
	public string $id;
	public ?string $secureObjectHash = null;
	public ?string $internetMessageId = null;
	public ?string $subject = null;
	public ?string $from_name = null;
	public ?string $from_email = null;
	public ?string $sender_name = null;
	public ?string $sender_email = null;
	public ?string $to_recipients = null;
	public ?string $cc_recipients = null;
	public ?string $bcc_recipients = null;
	public ?string $received_datetime = null;
	public ?string $sent_datetime = null;
	public ?string $body = null;
	public ?string $body_type = null;
	public ?string $body_preview = null;
	public ?string $importance = null;
	public ?string $conversation_id = null;
	public ?int $has_attachments = null;
	public ?string $mail_headers_raw = null;

	/** @var array<string, string[]> */
	public array $headers = [];

	private ?array $azureData = null;
	public ?string $userEmail = null;

	/**
	 * Creates a GraphMail object from Microsoft Graph API mail data.
	 *
	 * @param array       $item The email data from the Graph API
	 * @param string|null $userEmail
	 * @return GraphMail
	 */
	public static function fromGraphData(array $item, ?string $userEmail = null): self
	{
		$m = new self();

		$m->id = $item['id'];
		$m->conversation_id = $item['conversationId'] ?? null;
		$m->secureObjectHash = hash("sha256", $m->id."_____".$m->conversation_id);

		$m->internetMessageId = $item['internetMessageId'] ?? null;
		$m->subject = $item['subject'] ?? null;

		$m->from_name = $item['from']['emailAddress']['name'] ?? null;
		$m->from_email = $item['from']['emailAddress']['address'] ?? null;

		$m->sender_name = $item['sender']['emailAddress']['name'] ?? $m->from_name;
		$m->sender_email = $item['sender']['emailAddress']['address'] ?? $m->from_email;

		$m->to_recipients = json_encode($item['toRecipients']);
		$m->cc_recipients = json_encode($item['ccRecipients']);
		$m->bcc_recipients = json_encode($item['bccRecipients']);

		$m->received_datetime = $item['receivedDateTime'] ?? null;
		$m->sent_datetime = $item['sentDateTime'] ?? null;
		$m->body = $item['body']['content'] ?? null;
		$m->body_type = $item['body']['contentType'] ?? null;
		$m->body_preview = $item['bodyPreview'] ?? null;
		$m->importance = $item['importance'] ?? null;
		$m->conversation_id = $item['conversationId'] ?? null;
		$m->has_attachments = (int)$item['hasAttachments'] ?? null;

		// Header-Handling
		if (!empty($item['internetMessageHeaders'])) {
			$raw = '';
			$parsed = [];
			foreach ($item['internetMessageHeaders'] as $header) {
				$name = $header['name'];
				$value = $header['value'];
				$raw .= "{$name}: {$value}\r\n";
				$parsed[$name][] = $value;
			}
			$m->mail_headers_raw = $raw;
			$m->headers = $parsed;
		}

		$m->azureData = $item; // Originaldaten werden gespeichert
		$m->userEmail = $userEmail;
		return $m;
	}

	/**
	 * Erstellt ein GraphMail Objekt aus einem JSON-String.
	 *
	 * @param string $json
	 * @return GraphMail
	 * @throws \InvalidArgumentException Wenn das JSON ungültig ist.
	 */
	public static function fromJson(string $json): self
	{
		$data = json_decode($json, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException('Ungültiges JSON: ' . json_last_error_msg());
		}
		return self::fromArray($data);
	}

	/**
	 * Erstellt ein GraphMail Objekt aus einem Array.
	 *
	 * @param array $data
	 * @return GraphMail
	 */
	public static function fromArray(array $data): self
	{
		return self::fromGraphData($data);
	}

	/**
	 * Erstellt ein GraphMail Objekt aus einem stdClass Objekt.
	 *
	 * @param \stdClass $obj
	 * @return GraphMail
	 */
	public static function fromStdClassObject(\stdClass $obj): self
	{
		return self::fromArray(json_decode(json_encode($obj), true));
	}

	public function toJson(): string
	{
		return json_encode($this);
	}


	/**
	 * Converts a GraphMail object into a Mail object.
	 *
	 * @return \Mail
	 */
	public function toMail(): \Mail
	{
		// Erstelle ein neues Mail Objekt, ohne Primary Key (0 oder null übergeben)
		$mail = new \Mail(0);

		// Übertrage die Werte
		$mail->SecureObjectHash = $this->secureObjectHash;
		$mail->AzureId = $this->id;
		$mail->MessageId = $this->internetMessageId;
		$mail->Subject = $this->subject;
		$mail->SenderName = $this->sender_name;
		$mail->SenderEmail = $this->sender_email;
		$mail->FromName = $this->from_name;
		$mail->FromEmail = $this->from_email;
		$mail->ToRecipients = $this->to_recipients;
		$mail->CcRecipients = $this->cc_recipients;
		$mail->BccRecipients = $this->bcc_recipients;
		$mail->Body = $this->body;
		$mail->BodyType = $this->body_type;
		$mail->ReceivedDatetime = $this->received_datetime;
		$mail->SentDatetime = $this->sent_datetime;
		$mail->Importance = $this->importance;
		$mail->ConversationId = $this->conversation_id;
		$mail->BodyPreview = $this->body_preview;
		$mail->HasAttachments = $this->has_attachments ? 1 : 0;
		$mail->MailHeadersRaw = $this->mail_headers_raw;
		$mail->HeadersJson = json_encode($this->headers);
		$mail->AzureObject = json_encode($this->azureData);
		$mail->CreatedAt = date("Y-m-d H:i:s");
		$mail->SourceMailbox = $this->userEmail;

		return $mail;
	}


}
