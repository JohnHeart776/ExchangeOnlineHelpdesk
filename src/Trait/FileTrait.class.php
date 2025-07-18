<?php

use JetBrains\PhpStorm\Pure;

trait FileTrait
{
	use JsonSerializableTrait;

	public function toJsonObject(): array
	{
		return array_merge($this->getBaseJsonFields(), [
			"name" => $this->getName(),
			"sha256" => $this->getHashSha256(),
			"link" => $this->getLink(),
		]);
	}

	public function getDataAsBase64()
	{
		return base64_encode($this->getDataDecoded());
	}

	public function getDataDecoded()
	{
		return gzuncompress(base64_decode($this->getData()));
	}

	public function getLink()
	{
		return "/file/" . $this->getGuid() . "/" . $this->getSecret1();
	}

	public function getExtension(): string
	{
		return pathinfo($this->getName(), PATHINFO_EXTENSION);
	}

	/**
	 * @return string
	 */

	public function getSizeWithUnit()
	{
		//get best unit from Size, show either KB or MB or GB
		$size = $this->getSize();
		if ($size >= 1024) {
			$sizeKB = round($size / 1024, 2);
			if ($sizeKB >= 1024) {
				$sizeMB = round($sizeKB / 1024, 2);
				if ($sizeMB >= 1024) {
					return round($sizeMB / 1024, 2) . " GB";
				}
				return $sizeMB . " MB";
			}
			return $sizeKB . " KB";
		}
		return $size . " B";
	}


}
