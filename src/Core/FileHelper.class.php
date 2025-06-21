<?php

class FileHelper
{


	/**
	 * @param array $file
	 * @return File|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function createFileFromUpload(array $fileData): ?File
	{
		$fileContent = file_get_contents($fileData['tmp_name']);

		$file = new File(0);

		$file->Data = base64_encode(gzcompress($fileContent));
		$file->Type = $fileData['type'];
		$file->Name = $fileData['name'];
		$file->Secret1 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret2 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret3 = hash("sha256", \GuidHelper::generateGuid());
		$file->HashSha256 = hash("sha256", $fileContent);
		$file->Size = strlen($fileContent);

		return FileController::save($file);
	}

	public static function createFileFromString(string $name, string $type, string $blob): ?File
	{
		$file = new File(0);

		$file->Data = base64_encode(gzcompress($blob));
		$file->Type = $type;
		$file->Name = $name;
		$file->Secret1 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret2 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret3 = hash("sha256", \GuidHelper::generateGuid());
		$file->HashSha256 = hash("sha256", $blob);
		$file->Size = strlen($blob);

		return FileController::save($file);
	}

	/**
	 * @param MailAttachment $mailAttachment
	 * @return File|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function createFileFromMailAttachment(MailAttachment $mailAttachment): ?File
	{
		$blob = $mailAttachment->getContentDecoded();
		$file = new File(0);
		$file->Data = base64_encode(gzcompress($blob));
		$file->Type = $mailAttachment->getContentType();
		$file->Name = $mailAttachment->getName();
		$file->Secret1 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret2 = hash("sha256", \GuidHelper::generateGuid());
		$file->Secret3 = hash("sha256", \GuidHelper::generateGuid());
		$file->HashSha256 = hash("sha256", $blob);
		$file->Size = strlen($blob);

		return FileController::save($file);
	}
}
