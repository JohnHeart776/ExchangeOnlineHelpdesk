<?php

class TimeSpan
{

	public ?float $TotalDays;
	public ?float $TotalHours;
	public ?float $TotalMinutes;
	public ?float $TotalSeconds;

	public ?DateTime $start;
	public ?DateTime $end;

	public ?DateInterval $interval;

	/**
	 * Creates a TimeSpan object from given start and end DateTime objects.
	 * It calculates the difference between the two dates, sets the respective properties,
	 * and stores the start and end times to class properties.
	 *
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @return self
	 */
	public static function fromDateTimeObjects(\DateTime $start, \DateTime $end): self
	{
		$interval = $start->diff($end);

		$instance = self::fromDateInterval($interval);
		$instance->start = $start;
		$instance->end = $end;

		return $instance;
	}

	/**
	 * Converts a PHP DateInterval object into a C#-like time interval object
	 * with "total*" fields (e.g., TotalDays, TotalHours, etc.) and returns a new instance of self.
	 *
	 * @param \DateInterval $interval
	 * @return self
	 */
	public static function fromDateInterval(\DateInterval $interval): self
	{

		$totalSeconds = ($interval->days * 24 * 60 * 60) +
			($interval->h * 60 * 60) +
			($interval->i * 60) +
			$interval->s;

		if ($interval->invert) {
			$totalSeconds = -$totalSeconds;
		}

		$instance = new self();
		$instance->interval = $interval;
		$instance->TotalDays = $totalSeconds / (24 * 60 * 60);
		$instance->TotalHours = $totalSeconds / (60 * 60);
		$instance->TotalMinutes = $totalSeconds / 60;
		$instance->TotalSeconds = $totalSeconds;

		return $instance;
	}

	public function __toString()
	{

		if (!$this->interval) {
			return "00:00:00";
		}

		$hours = $this->interval->days * 24 + $this->interval->h;
		$minutes = $this->interval->i;
		$seconds = $this->interval->s;

		if ($this->interval->invert) {
			$hours = -$hours;
			$minutes = -$minutes;
			$seconds = -$seconds;
		}

		return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
	}
	
}
