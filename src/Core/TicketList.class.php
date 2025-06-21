<?php

class TicketList
{

	public static function getLatestOpenTickets(?int $limit = null): array
	{
		$r = [];

		if (!$limit)
			$limit = 10000;

		if ($limit > 10000)
			$limit = 10000;

		$_q = "SELECT t.TicketId 
				FROM Ticket t 
				LEFT JOIN Status s ON t.StatusId = s.StatusId 
				WHERE 1 
				  AND (s.IsOpen = 1)
				ORDER BY t.CreatedDatetime DESC 
			LIMIT $limit";

		global $d;
		$t = $d->get($_q);
		foreach ($t as $u) {
			$r[] = new Ticket((int)$u["TicketId"]);
		}
		return $r;
	}

	/**
	 * @return Ticket[]
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getAllTickets(): array
	{
		global $d;
		$_q = "SELECT TicketId FROM Ticket ORDER BY TicketId DESC";
		$t = $d->get($_q);
		$r = [];
		foreach ($t as $u) {
			$r[] = new Ticket((int)$u["TicketId"]);
		}
		return $r;
	}

	/**
	 * @return Ticket[]
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDueTickets(): array
	{
		global $d;
		$_q = "SELECT TicketId 
				FROM Ticket t
				LEFT JOIN Status s ON t.StatusId = s.StatusId
                WHERE t.DueDatetime < NOW()
                AND (s.IsFinal IS NULL OR s.IsFinal = 0)
                ORDER BY t.DueDatetime ASC";
		$t = $d->get($_q);
		$r = [];
		foreach ($t as $u) {
			$r[] = new Ticket((int)$u["TicketId"]);
		}
		return $r;
	}

}
