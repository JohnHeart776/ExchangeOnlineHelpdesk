<?php

class SearchHelper
{
	/**
	 * Führt eine Mehrwortsuche für Organisation-Users aus.
	 *
	 * @param mixed $query
	 *
	 * @return OrganizationUser[]
	 */
	public static function searchOrganizationUsers(mixed $query): array
	{
		global $d;

		// Beispiel: "John Doe Manager"
		// => suchet alle Datensätze, in denen 'DisplayName' "John" UND "Doe" UND "Manager" enthält.

		// In Einzelwörter zerteilen
		$words = array_filter(explode(' ', (string)$query));

		// Falls keine Wörter vorhanden sind, leere Liste zurückgeben (oder anders handeln)
		if (empty($words)) {
			return [];
		}

		// Bedingungen dynamisch aufbauen
		$conditions = [];
		$params = [];
		foreach ($words as $index => $word) {
			$conditions[] = "DisplayName LIKE :word{$index}";
			$params[":word{$index}"] = '%' . $word . '%';
		}

		// WHERE-Bedingungen zusammenführen
		// Hier "AND", damit alle Suchworte gefunden werden müssen
		$whereClause = implode(' AND ', $conditions);

		// Komplette Query
		$_q = "SELECT OrganizationUserId
                FROM OrganizationUser
                WHERE $whereClause";

		$t = $d->getPDO($_q, $params);

		$r = [];
		foreach ($t as $u) {
			$r[] = new OrganizationUser((int)$u["OrganizationUserId"]);
		}

		return $r;
	}
}
