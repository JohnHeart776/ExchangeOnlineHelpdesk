<?php

class CalendarRange
{
	private array $days = [];

	public function __construct(
		public DateTime $start,
		public DateTime $end)
	{
		if ($this->start > $this->end) {
			[$this->start, $this->end] = [$this->end, $this->start];
		}

		$current = clone $start;
		$current->setTime(0, 0);

		while ($current <= $this->end) {
			$this->days[] = clone $current;
			$current->modify('+1 day');
		}
	}

	/**
	 * @return DateTime[]
	 */
	public function getDays(): array
	{
		return $this->days;
	}


	public function isToday(DateTime $date): bool
	{
		$today = new DateTime();
		return $date->format('Y-m-d') === $today->format('Y-m-d');
	}
}

