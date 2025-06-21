<?php

class ReportingTicketsByDayAndTime
{
	private const MAX_MONTHS = 12;
	private const DEFAULT_RANGE_MONTHS = 3; // used when $end is omitted

	public function __construct(
		public DateTime  $start,
		public ?DateTime $end = null,
	)
	{
		$this->start->setTime(0, 0, 0);
		if (!$this->end)
			$this->end = $this->createEndDateFromStart($start);

		$this->assertMaxRange();
	}

	public function getTimeScatterStats(): array
	{
		$_q = "
			SELECT 
				DAYOFWEEK(t.CreatedDatetime) as dayOfWeek,
				HOUR(t.CreatedDatetime) as hourOfDay,
				COUNT(t.TicketId) as ticketCount
			FROM Ticket t
			LEFT JOIN Status s ON t.StatusId = s.StatusId 
			WHERE t.CreatedDatetime BETWEEN :start AND :end
				AND s.IsOpen = 1
			GROUP BY 
				DAYOFWEEK(t.CreatedDatetime),
				HOUR(t.CreatedDatetime)
			ORDER BY dayOfWeek ASC, hourOfDay ASC
		";

		global $d;

		$t = $d->getPDO($_q, [
			':start' => $this->start->format(DateTimeInterface::ATOM),
			':end' => $this->end->format(DateTimeInterface::ATOM),
		]);

		$stats = [];
		foreach ($t as $row) {
			$stats[] = new ReportingTicketsByDayAndTimeElement(
				(int)$row["dayOfWeek"],
				(int)$row["hourOfDay"],
				(int)$row["ticketCount"]
			);
		}

		return $stats;
	}

	private function createEndDateFromStart(DateTime $start): DateTime
	{
		return (clone $start)
			->add(new DateInterval(sprintf('P%dM', self::DEFAULT_RANGE_MONTHS)))
			->sub(new DateInterval('PT1S'));
	}

	private function assertMaxRange(): void
	{
		$monthsApart = ($this->end->diff($this->start))->y * 12
			+ ($this->end->diff($this->start))->m;

		if ($monthsApart > self::MAX_MONTHS) {
			throw new RuntimeException(sprintf(
				'Time-frame too large: %d months given (maximum allowed: %d).',
				$monthsApart,
				self::MAX_MONTHS
			));
		}

		if ($this->end < $this->start) {
			throw new RuntimeException('The end date must not be earlier than the start date.');
		}
	}
}

class ReportingTicketsByDayAndTimeElement
{
	public function __construct(
		public int $dayOfWeek,
		public int $hourOfDay,
		public int $ticketCount
	)
	{
	}
}
