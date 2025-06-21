<?php

class ReportingAgentDistribution
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

	public function getDistribution(): array
	{
		$_q = "
            SELECT
                COUNT(t.TicketId) AS ticketCount,
                u.UserId
            FROM Ticket t
            LEFT JOIN User u ON t.AssigneeUserId = u.UserId
            LEFT JOIN Status s ON t.StatusId = s.StatusId
            WHERE t.CreatedDatetime BETWEEN :start AND :end
            AND s.IsFinal = 1 
            GROUP BY t.AssigneeUserId, u.DisplayName
            ORDER BY ticketCount DESC, u.DisplayName ASC;
        ";

		global $d;

		$t = $d->getPDO($_q, [
			':start' => $this->start->format(DateTimeInterface::ATOM),
			':end' => $this->end->format(DateTimeInterface::ATOM),
		]);


		// Cast the counts to int for strictness
		$distribution = [];
		foreach ($t as $u) {
			$user = new User((int)$u["UserId"]);
			$distribution[] = new ReportingAgentDistributionElement(
				($user->isValid()?$user:null), (int)$u["ticketCount"],
			);
		}

		return $distribution;
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

class ReportingAgentDistributionElement{
	public function __construct(
		public ?User $user,
		public int  $count,
	){

	}
}
