<?php

class MenuController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return Menu[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MenuId` FROM `Menu`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "MenuId";
            }

            $_q .= " ORDER BY `" . $d->filter($sortBy) . "` " . $direction;
        }

        if ($limit > 0) {
            $_q .= " LIMIT " . $d->filter($limit);
        }
        $_q .= ";";

        $t = $d->get($_q);
        $r = [];
        foreach ($t as $u) {
            $r[] = new Menu((int)$u["MenuId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Menu|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Menu
    {
        global $d;
        $_q = "SELECT `MenuId` FROM `Menu` WHERE `MenuId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Menu((int)$t["MenuId"]);
    }

    /**
     * @param string $guid
     * @return Menu|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Menu
    {
        global $d;
        $_q = "SELECT `MenuId` FROM `Menu` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Menu($t["MenuId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Menu|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Menu
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Menu|null|Menu[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MenuId", "Guid", "Enabled", "Name", "SortNum"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `MenuId` FROM `Menu` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        // Apply optional limit if not fetchOne
        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new Menu((int)$result["MenuId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Menu((int)$row["MenuId"]);
            }
            return $arr;
        }
    }

    /**
	 * Checks if an element exists with the given search term.
	 *
     * @param string $field
     * @param string $term
     * @return bool
     * @throws \Database\DatabaseQueryException
     */
    public static function exist(string $field, string $term): bool
    {
        global $d;
        $allowed = ["MenuId", "Guid", "Enabled", "Name", "SortNum"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MenuId` FROM `Menu` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return Menu[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `MenuId` FROM `Menu` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Menu((int)$row["MenuId"]);
        }
        return $arr;
    }

    /**
     * @param Menu $obj
     * @return Menu|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Menu $obj): ?Menu {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->MenuId)) {
			throw new Exception("Primary Key must be empty");
		}
		if (!empty($obj->Guid)) {
			throw new Exception("GUID must be empty");
		}
		// Set CreatedAt and UpdatedAt with dynamic SQL value NOW() if available
		// Build the INSERT query. All columns except the Primary Key are used.
		$cols = [];
        $vals = [];
        $cols[] = "`Guid`";
        $vals[] = "UUID()";
        if (isset($obj->Enabled)) {
            $cols[] = "`Enabled`";
            $vals[] = "\"".$d->filter($obj->Enabled)."\"";
        }
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->SortNum)) {
            $cols[] = "`SortNum`";
            $vals[] = "\"".$d->filter($obj->SortNum)."\"";
        }
        $_q = "INSERT INTO `Menu` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Menu((int)$id);
    }

}
