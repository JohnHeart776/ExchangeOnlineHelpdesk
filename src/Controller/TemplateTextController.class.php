<?php

class TemplateTextController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return TemplateText[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TemplateTextId` FROM `TemplateText`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "TemplateTextId";
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
            $r[] = new TemplateText((int)$u["TemplateTextId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TemplateText|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TemplateText
    {
        global $d;
        $_q = "SELECT `TemplateTextId` FROM `TemplateText` WHERE `TemplateTextId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TemplateText((int)$t["TemplateTextId"]);
    }

    /**
     * @param string $guid
     * @return TemplateText|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TemplateText
    {
        global $d;
        $_q = "SELECT `TemplateTextId` FROM `TemplateText` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TemplateText($t["TemplateTextId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TemplateText|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TemplateText
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TemplateText|null|TemplateText[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TemplateTextId", "Guid", "Name", "Description", "Content", "CreatedDatetime"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `TemplateTextId` FROM `TemplateText` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TemplateText((int)$result["TemplateTextId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TemplateText((int)$row["TemplateTextId"]);
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
        $allowed = ["TemplateTextId", "Guid", "Name", "Description", "Content", "CreatedDatetime"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TemplateTextId` FROM `TemplateText` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return TemplateText[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `TemplateTextId` FROM `TemplateText` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TemplateText((int)$row["TemplateTextId"]);
        }
        return $arr;
    }

    /**
     * @param TemplateText $obj
     * @return TemplateText|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TemplateText $obj): ?TemplateText {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->TemplateTextId)) {
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
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->Description)) {
            $cols[] = "`Description`";
            $vals[] = "\"".$d->filter($obj->Description)."\"";
        }
        if (isset($obj->Content)) {
            $cols[] = "`Content`";
            $vals[] = "\"".$d->filter($obj->Content)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        $_q = "INSERT INTO `TemplateText` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TemplateText((int)$id);
    }

}
