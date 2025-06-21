<?php

class DateEta
{
	private DateTime $targetDate;
	private DateTime $currentDate;
	private DateInterval $difference;

	public function __construct(DateTime $targetDate, ?DateTime $currentDate = null)
	{
		$this->targetDate = $targetDate;
		$this->currentDate = $currentDate ?? new DateTime();
		$this->difference = $this->targetDate->diff($this->currentDate);
	}

	public function totalSeconds(): int
	{
		return ($this->difference->days * 86400) +
			($this->difference->h * 3600) +
			($this->difference->i * 60) +
			$this->difference->s;
	}

	public function totalMinutes(): int
	{
		return ($this->difference->days * 1440) +
			($this->difference->h * 60) +
			$this->difference->i;
	}

	public function totalHours(): int
	{
		return ($this->difference->days * 24) + $this->difference->h;
	}

	public function totalDays(): int
	{
		return $this->difference->days;
	}

	public function totalWeeks(): int
	{
		return (int)floor($this->difference->days / 7);
	}

	public function toEtaString(string $locale = 'DE'): string
	{
		$translations = [
			'DE' => [
				'weeks' => 'Wochen',
				'week' => 'Woche',
				'days' => 'Tage',
				'day' => 'Tag',
				'hours' => 'Stunden',
				'hour' => 'Stunde',
				'minutes' => 'Minuten',
				'minute' => 'Minute',
				'seconds' => 'Sekunden',
				'second' => 'Sekunde',
			],
			'EN' => [
				'weeks' => 'weeks',
				'week' => 'week',
				'days' => 'days',
				'day' => 'day',
				'hours' => 'hours',
				'hour' => 'hour',
				'minutes' => 'minutes',
				'minute' => 'minute',
				'seconds' => 'seconds',
				'second' => 'second',
			],
		];

		$locale = strtoupper($locale);
		$t = $translations[$locale] ?? $translations['EN'];

		if ($this->totalWeeks() >= 2) {
			return sprintf("%d %s", $this->totalWeeks(), $t['weeks']);
		}
		if ($this->totalWeeks() == 1) {
			$remainingDays = $this->totalDays() % 7;
			return $remainingDays > 0 ?
				sprintf("1 %s, %d %s", $t['week'], $remainingDays, $t['days']) :
				sprintf("1 %s", $t['week']);
		}
		if ($this->totalDays() > 1) {
			$hours = $this->difference->h;
			return $hours > 12 ?
				sprintf("%d %s", $this->totalDays(), $t['days']) :
				sprintf("%d %s, %d %s", $this->totalDays(), $t['days'], $hours, $t['hours']);
		}
		if ($this->totalDays() == 1) {
			return sprintf("1 %s", $t['day']);
		}
		if ($this->totalHours() > 0) {
			return sprintf("%d %s", $this->totalHours(), $t['hours']);
		}
		if ($this->totalMinutes() > 0) {
			return sprintf("%d %s", $this->totalMinutes(), $t['minutes']);
		}
		return sprintf("%d %s", $this->totalSeconds(), $t['seconds']);
	}

	public function __toString(): string
	{
		return $this->toEtaString();
	}

}
