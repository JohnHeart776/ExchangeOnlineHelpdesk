<?php

trait MailAttachmentTrait
{

	/**
	 * @param Mail $mail
	 * @return bool
	 * @throws Exception
	 */
	public function linkToMail(Mail $mail): bool
	{
		$updateResult = $this->update("MailId", $mail->MailId);
		if (!$updateResult)
			throw new \Exception("Fehler beim Speichern des MailAttachments");
		$this->spawn();
		return $updateResult;
	}

	public function getFileNameWithoutExtension(): string
	{
		return pathinfo($this->Name, PATHINFO_FILENAME);
	}

	public function getFileExtension(): string
	{
		return strtolower(pathinfo($this->Name, PATHINFO_EXTENSION));
	}

	public function getSizeWithUnit()
	{

		$units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		$size = $this->Size;
		$unitIndex = 0;

		while ($size >= 1024 && $unitIndex < count($units) - 1) {
			$size /= 1024;
			$unitIndex++;
		}

		return round($size, 2) . ' ' . $units[$unitIndex];
	}

	public function getPublicDownloadLink(): string
	{
		return "/mail/attachment/download/" . $this->getGuid() . "/" . $this->getSecret1();
	}

	public function getPublicTextLink(): string
	{
		return "/mail/attachment/text/" . $this->getGuid() . "/" . $this->getSecret2();
	}

	/**
	 * @return false|string
	 */
	public function getContentDecoded(): false|string
	{
		return base64_decode($this->Content);
	}

	public function isOfTypeText(): bool
	{
		$p = explode("/", $this->getContentType())[0];
		return $p == "text";
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->Name,
			"size" => $this->getSizeWithUnit(),
			"type" => $this->getContentType(),
			"downloadLink" => $this->getPublicDownloadLink(),
		];
	}

	/**
	 * @return MailAttachmentIgnore
	 */
	public function toMailAttachmentIgnoreEnvelope(): MailAttachmentIgnore
	{
		$mai = new MailAttachmentIgnore(0);
		$mai->Enabled = 1;
		$mai->HashSha256 = $this->getHashSha256();
		$mai->CreatedAt = new DateTime()->format(DATE_ATOM);
		return $mai;
	}

	public function isIgnored()
	{
		return MailAttachmentIgnore::isMailAttachmentIgnored($this);
	}

	public function hasTextRepresentation(): bool
	{
		return $this->TextRepresentation != null && $this->TextRepresentation != "";
	}

	public function isPDF() {
		return $this->getContentType() == "application/pdf";
	}

	public function isImage() {
		//return true for png, gif and jpeg, jpg
		return $this->getContentType() == "image/png" || $this->getContentType() == "image/gif" || $this->getContentType() == "image/jpeg" || $this->getContentType() == "image/jpg";
	}

	public function canBeRepresentedInTextUsingAi(): bool
	{
		return $this->isPDF() || $this->isImage();
	}


	public function generateTextRepresentation(bool $force = false) {

		if (!$force && !empty($this->hasTextRepresentation()) && strlen($this->getTextRepresentation())>10) {
			return $this->TextRepresentation;
		}

		if (!$this->canBeRepresentedInTextUsingAi()) {
			return "";
		}

		//generate the represenations

		global $d;
		//look if any other attachment with same hash hash a text representation
		$_q = "SELECT TextRepresentation 
			FROM MailAttachment 
			WHERE TextRepresentation IS NOT NULL 
			  AND HashSha256 = '" . $this->getHashSha256() . "'
			   AND MailAttachmentId !=" . $this->getMailAttachmentId() . " 
		   	LIMIT 1";

		$t = $d->get($_q, true);
		if (!$force && !empty($t)) {
			//another file already has a textual represenation
			$this->update("TextRepresentation", $t['TextRepresentation']);
			$this->spawn();

		} else {


			$aiClient = AiService::getClient();
			$messages = $aiClient->getInitialPayload(false);


			if ($this->isPDF()) {
				$messages[] = AiService::createPayloadElement("user", [
					[
						"type" => "text",
						"text" => "Analyze this File, using built in vision. Create a textual representation of the file, describe its content and text, extract text and provide a detailed explanation of the file. The Description text shall be in english Langauge, actual text contain shall be displayed in the native language plus an translated version in english. ",
					],
					[
						"type" => "file",
						"file" => [
							"file_data" => "data:" . $this->getContentType() . ";base64," . $this->getContent(),
							"filename" => $this->getName(),
						],
					],
				]);
			} else if ($this->isImage()) {
				$messages[] = AiService::createPayloadElement("user", [
					[
						"type" => "text",
						"text" => "Analyze this Image, using built in vision. Create a textual representation of the image, describe its content and text, extract text and provide a detailed explanation of the image. The Description text shall be in english Langauge, actual text contain shall be displayed in the native language plus an translated version in english",
					],
					[
						"type" => "image_url",
						"image_url" => [
							"url" => "data:" . $this->getContentType() . ";base64," . $this->getContent(),
						],
					],
				]);
			}

			$response = $aiClient->getResponseForMessageArray($messages);
			$textRep = $response->choices[0]->message->content ?? ["no text representation"];
			$this->update("TextRepresentation", $textRep);
			$this->spawn();
		}

		return $this->generateTextRepresentation();

	}

}