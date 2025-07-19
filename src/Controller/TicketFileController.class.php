<?php

class TicketFileController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return TicketFile[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketFileId` FROM `TicketFile`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "TicketFileId";
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
            $r[] = new TicketFile((int)$u["TicketFileId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TicketFile|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TicketFile
    {
        global $d;
        $_q = "SELECT `TicketFileId` FROM `TicketFile` WHERE `TicketFileId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketFile((int)$t["TicketFileId"]);
    }

    /**
     * @param string $guid
     * @return TicketFile|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TicketFile
    {
        global $d;
        $_q = "SELECT `TicketFileId` FROM `TicketFile` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketFile($t["TicketFileId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TicketFile|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TicketFile
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TicketFile|null|TicketFile[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketFileId", "Guid", "TicketId", "FileId", "UserId", "CreatedDatetime", "AccessLevel"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `TicketFileId` FROM `TicketFile` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TicketFile((int)$result["TicketFileId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TicketFile((int)$row["TicketFileId"]);
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
        $allowed = ["TicketFileId", "Guid", "TicketId", "FileId", "UserId", "CreatedDatetime", "AccessLevel"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketFileId` FROM `TicketFile` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return TicketFile[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketFileId` FROM `TicketFile` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TicketFile((int)$row["TicketFileId"]);
        }
        return $arr;
    }

    /**
     * @param TicketFile $obj
     * @return TicketFile|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TicketFile $obj): ?TicketFile {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->TicketFileId)) {
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
        if (isset($obj->TicketId)) {
            $cols[] = "`TicketId`";
            $vals[] = "\"".$d->filter($obj->TicketId)."\"";
        }
        if (isset($obj->FileId)) {
            $cols[] = "`FileId`";
            $vals[] = "\"".$d->filter($obj->FileId)."\"";
        }
        if (isset($obj->UserId)) {
            $cols[] = "`UserId`";
            $vals[] = "\"".$d->filter($obj->UserId)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->AccessLevel)) {
            $cols[] = "`AccessLevel`";
            $vals[] = "\"".$d->filter($obj->AccessLevel)."\"";
        }
        $_q = "INSERT INTO `TicketFile` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TicketFile((int)$id);
    }

}
