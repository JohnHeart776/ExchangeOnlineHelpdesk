<?php

class CategoryHelper
{
	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultId(): ?int
	{
		global $d;
		$sql = "SELECT CategoryId FROM Category WHERE isDefault = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['CategoryId'] : null;
	}

	/**
	 * @return Category|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultCategory(): ?Category
	{
		return new Category(self::getDefaultId());
	}

	/**
	 * @param Ticket $ticket
	 * @return CategorySuggestion|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function suggestCategory(Ticket $ticket): ?CategorySuggestion
	{
		global $d;

		$text = $ticket->extractAllText();

		$sql = "SELECT CategorySuggestionId
	        FROM CategorySuggestion
	        WHERE enabled = 1 
	        AND CategoryId > 0
	        ORDER BY Priority DESC, LENGTH(filter) DESC";

		$suggestions = $d->getPDO($sql);

		foreach ($suggestions as $row) {
			$tcs = new CategorySuggestion((int)$row['CategorySuggestionId']);
			if ($tcs->matches($text)) {
				return $tcs;
			}
		}

		return null;
	}

}
