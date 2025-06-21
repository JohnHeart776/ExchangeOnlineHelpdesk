<?php

class DateTimeHelper
{

	public static function getNow(): DateTime
	{
		return new DateTime();
	}

	public static function getWeekdayInGerman(DateTime $date): string
	{
		$weekdays = [
			1 => 'Montag',
			2 => 'Dienstag',
			3 => 'Mittwoch',
			4 => 'Donnerstag',
			5 => 'Freitag',
			6 => 'Samstag',
			7 => 'Sonntag',
		];

		return $weekdays[(int)$date->format('N')];
	}

}
