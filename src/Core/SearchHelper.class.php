<?php

class SearchHelper
{
	/**
	 * Performs a multi-word search for organization users.
	 *
	 * @param mixed $query
	 *
	 * @return OrganizationUser[]
	 */
	public static function searchOrganizationUsers(mixed $query): array
	{
		global $d;

		// Example: "John Doe Manager"
		// => searches all records where 'DisplayName' contains "John" AND "Doe" AND "Manager".

		// Split into individual words
		$words = array_filter(explode(' ', (string)$query));

		// If no words are present, return empty list (or handle differently)
		if (empty($words)) {
			return [];
		}

		// Build conditions dynamically
		$conditions = [];
		$params = [];
		foreach ($words as $index => $word) {
			$conditions[] = "DisplayName LIKE :word{$index}";
			$params[":word{$index}"] = '%' . $word . '%';
		}

		// Merge WHERE conditions
		// Here "AND" so that all search words must be found
		$whereClause = implode(' AND ', $conditions);

		// Complete query
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
