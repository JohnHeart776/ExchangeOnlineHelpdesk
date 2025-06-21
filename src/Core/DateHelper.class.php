<?php

class DateHelper
{

	/**
	 * @param string|null $dtString
	 * @return DateTime
	 * @throws DateMalformedStringException
	 */
	public static function getDate(?string $dtString = null): DateTime
	{
		if ($dtString) {
			return new DateTime($dtString);
		}
		return new DateTime();
	}

	/**
	 * @throws DateMalformedStringException
	 */
	public static function getDateTimeForMysql(?string $dtString = null): string
	{
		return self::getDate($dtString)->format("Y-m-d H:i:s");
	}
}
