<?php

class SlaHelper
{


	/**
	 * @throws DateMalformedStringException
	 * @throws \Database\DatabaseQueryException
	 * @throws DateMalformedIntervalStringException
	 */
	public static function getSlaDueDate(?\DateTime $now = null): DateTime
	{
		if (!$now)
			$now = new DateTime();

		$now = clone $now;
		if (self::isWithinBusinessTime($now)) {

			//add the default sla time
			$targetDate = $now->add(
				new DateInterval(
					Config::getConfigValueFor("sla.reaction.interval")
				)
			);

			//check if the target date is within regular business times
			if (self::isWithinBusinessTime($targetDate))
				return $targetDate;
			else {
				$targetDate = self::getNextBusinessDay($now)
					->setTime(config::getConfigValueFor("sla.business.hours.from"), 0, 0)
					->add(new \DateInterval(config::getConfigValueFor("sla.reaction.interval")));
			}
		} else {
			$targetDate = self::getNextBusinessDay($now)
				->setTime(config::getConfigValueFor("sla.business.hours.from"), 0, 0)
				->add(new DateInterval(config::getConfigValueFor("sla.reaction.interval")));
		}
		return $targetDate;

	}

	public static function isWithinBusinessTime(\DateTime $dateTime)
	{
		return self::isWithinBusinessHours($dateTime) && self::IsWithinBusinessDays($dateTime);
	}

	private static function isWithinBusinessHours(DateTime $dateTime)
	{
		$hoursFrom = (int)config::getConfigValueFor("sla.business.hours.from");
		$hoursTo = (int)config::getConfigValueFor("sla.business.hours.to");
		$hoursNow = (int)$dateTime->format("H");
		return ($hoursFrom <= $hoursNow && $hoursNow <= $hoursTo);
	}

	private static function IsWithinBusinessDays(DateTime $dateTime)
	{
		$daysFrom = config::getConfigValueFor("sla.business.days.from");
		$daysTo = config::getConfigValueFor("sla.business.days.to");
		$daysNow = (int)$dateTime->format("N");
		return ($daysFrom <= $daysNow && $daysNow <= $daysTo);
	}

	private static function getTodaysSlaStart(\DateTime $now): DateTime
	{
		return $now->setTime(config::getConfigValueFor("sla.business.hours.from"), 0, 0);
	}

	private static function getDaysSlaEnd(\DateTime $now): DateTime
	{
		return $now->setTime(config::getConfigValueFor("sla.business.hours.to"), 0, 0);
	}


	private static function getNextBusinessDay(\DateTime $now): DateTime
	{
		$now->modify("+1 day");
		while (!self::IsWithinBusinessDays($now)) {
			$now->modify("+1 day");
		}
		return $now;
	}

}
