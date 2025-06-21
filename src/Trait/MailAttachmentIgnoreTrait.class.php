<?php

trait MailAttachmentIgnoreTrait
{

	public static function isMailAttachmentIgnored(MailAttachment $mailAttachment): bool
	{
		$hash = $mailAttachment->getHashSha256();
		$existCheck = MailAttachmentIgnoreController::searchOneBy("HashSha256", $hash);
		return $existCheck !== null;
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"hashSha256" => $this->getHashSha256(),
		];
	}

}
