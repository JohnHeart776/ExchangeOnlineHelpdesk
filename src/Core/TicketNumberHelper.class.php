<?php

class TicketNumberHelper
{
	public static function getNextTicketNumber(): string
	{
		global $d;
		$year = date('Y');
		$_q = "SELECT RIGHT(TicketNumber, 6) as lastNum FROM Ticket WHERE TicketNumber LIKE \"$year%\" ORDER BY TicketNumber DESC LIMIT 1";
		$t = $d->get($_q, true);
		$hex = strtoupper(dechex($t ? hexdec($t["lastNum"]) + 1 : 1));
		$hex = str_pad($hex, 6, '0', STR_PAD_LEFT);
		return $year . $hex;
	}

	public static function stringContainsTicketMarker(string $str): bool
	{

		$pattern = '/\[\[##([A-F0-9]+)##\]\]/';
		return preg_match($pattern, $str) === 1;
	}

	public static function extractTicketNumberFromString(string $str): ?string
	{
		$pattern = '/\[\[##([A-F0-9]+)##\]\]/';
		if (preg_match($pattern, $str, $matches)) {
			return $matches[1];
		}
		return null;
	}
}
