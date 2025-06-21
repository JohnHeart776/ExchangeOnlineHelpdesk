<?php

class AgentWrapper
{
	private array $ticketCache = [];

	public function __construct(
		private User      $user,
		private ?DateTime $cacheTime = null,
	)
	{

		if (!$this->cacheTime)
			$this->cacheTime = new DateTime('-2 years');


		$this->initializeCache();
	}

	private function initializeCache(): void
	{

		global $d;
		$_q = "SELECT TicketId
				FROM Ticket
				WHERE AssigneeUserId = :userId
				  AND CreatedDatetime >= :cacheTime 
				ORDER BY CreatedDatetime DESC
				LIMIT 100000
				";

		$t = $d->getPDO($_q, [
			"userId" => $this->user->getUserId(),
			"cacheTime" => $this->cacheTime->format("Y-m-d H:i:s"),
		]);

		foreach ($t as $u) {
			$this->ticketCache[] = new Ticket((int)$u["TicketId"]);
		}

	}

	/**
	 * @return Ticket[]
	 */
	public function getTickets(): array
	{
		return $this->ticketCache;
	}

	/**
	 * @return Ticket[]
	 */
	public function getAllTickets(): array
	{
		return $this->ticketCache;
	}

	public function countTickets(): int
	{
		return count($this->ticketCache);
	}

	/**
	 * @return float
	 */
	public function countTicketCompletePercentage(int $decimals = 7): float
	{
		return round($this->countOpenDueTickets() / $this->countTickets() * 100, $decimals);
	}

	/**
	 * @return int
	 */
	public function countOpenTickets(): int
	{
		return count($this->getOpenTickets());
	}

	/**
	 * @return Ticket[]
	 */
	public function getOpenTickets(): array
	{
		return array_filter(
			$this->ticketCache, fn($ticket) => $ticket->isOpen()
		);
	}


	public function countOpenDueTickets(): int
	{
		return count($this->getOpenDueTickets());
	}

	/**
	 * @return Ticket[]
	 */
	public function getOpenDueTickets(): array
	{
		return array_filter(
			$this->ticketCache,
			fn($ticket) => $ticket->isOpen() && $ticket->isDue()
		);
	}

	/**
	 * @param int $daysOffset
	 * @return Ticket[]
	 */
	public function ticketsByDay(int $daysOffset): array
	{
		$targetDate = date('Y-m-d', strtotime($daysOffset . ' days'));

		return array_filter(
			$this->ticketCache,
			fn($ticket) => date('Y-m-d', strtotime($ticket->getCreatedAt())) === $targetDate
		);
	}

	/**
	 * @param DateTime $dateTime
	 * @return Ticket[]
	 */
	public function getTicketsOfDay(DateTime $dateTime): array
	{
		return array_filter(
			$this->ticketCache,
			fn($ticket) => $ticket->getCreatedAt() === $dateTime->format("Y-m-d")
		);
	}

	/**
	 * @return TicketCalendarDay[]
	 * @throws DateMalformedStringException
	 */
	public function getTicketCalendarDays(?DateTime $start = null, ?DateTime $end = null): array
	{
		$result = [];
		$startDate = $start ?? new DateTime('-3 days');
		$startDate->setTime(0, 0, 0);

		$endDate = $end ?? new DateTime('+7 days');
		$endDate->setTime(23, 59, 59);

		for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {

			$tickets = array_filter(
				$this->ticketCache,
				fn($ticket) => $ticket->getCreatedDatetimeAsDateTime()->format("Y-m-d") === $date->format('Y-m-d')
			);

			usort($tickets, fn($a, $b) => strcmp($b->getCreatedDatetime(), $a->getCreatedDatetime()));

			$result[] = new TicketCalendarDay(clone $date, $tickets);
		}
		return $result;
	}

}
