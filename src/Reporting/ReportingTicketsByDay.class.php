<?php

class ReportingTicketsByDay
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

	public function getDailyStats(): array
	{
		$_q = "
	        SELECT 
	            DATE(t.CreatedDatetime) as date,
				COUNT(t.TicketId) as totalCount,
				SUM(s.IsFinal) as closedCount,
				SUM(s.IsOpen) as openCount
	        FROM Ticket t
	        LEFT JOIN Status s ON t.StatusId = s.StatusId
	        WHERE t.CreatedDatetime BETWEEN :start AND :end
	        GROUP BY DATE(t.CreatedDatetime)
	        ORDER BY date ASC;
	    ";

		global $d;

		$t = $d->getPDO($_q, [
			':start' => $this->start->format(DateTimeInterface::ATOM),
			':end' => $this->end->format(DateTimeInterface::ATOM),
		]);

		$stats = [];
		foreach ($t as $row) {
			$stats[] = new ReportingTicketsByDayElement(
				new DateTime($row["date"]),
				(int)$row["totalCount"],
				(int)$row["closedCount"],
				(int)$row["openCount"]
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

class ReportingTicketsByDayElement
{
	public function __construct(
		public DateTime $date,
		public int      $totalCount,
		public int      $closedCount,
		public int      $openCount,
	)
	{
	}
}
