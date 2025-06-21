<?php

class TicketCalendarDay
{
	public function __construct(
		private DateTime $day,
		private array    $tickets
	)
	{
	}

	public function getDay(): DateTime
	{
		return $this->day;
	}

	public function isToday(): bool
	{
		return $this->getDay()->format("Y-m-d") === date("Y-m-d");
	}

	public function getTickets(): array
	{
		return $this->tickets;
	}
}
