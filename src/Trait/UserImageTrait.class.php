<?php

trait UserImageTrait
{
	public function getPhotoDecoded(): false|string
	{
		return base64_decode($this->getBase64Image());
	}
}