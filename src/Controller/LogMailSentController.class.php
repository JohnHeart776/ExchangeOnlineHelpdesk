<?php

class LogMailSentController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return LogMailSent[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent`";

        // Apply sorting only if $direction is set
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "LogMailSentId";
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
            $r[] = new LogMailSent((int)$u["LogMailSentId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return LogMailSent|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?LogMailSent
    {
        global $d;
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent` WHERE `LogMailSentId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new LogMailSent((int)$t["LogMailSentId"]);
    }

    /**
     * @param string $guid
     * @return LogMailSent|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?LogMailSent
    {
        global $d;
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new LogMailSent($t["LogMailSentId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return LogMailSent|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?LogMailSent
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optional limit (default: 0 = no limit)
     * @return LogMailSent|null|LogMailSent[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["LogMailSentId", "Guid", "UserId", "Recipient", "Subject", "Body", "Created"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new LogMailSent((int)$result["LogMailSentId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new LogMailSent((int)$row["LogMailSentId"]);
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
        $allowed = ["LogMailSentId", "Guid", "UserId", "Recipient", "Subject", "Body", "Created"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Invalid search field: " . $field);
        }
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return LogMailSent[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `LogMailSentId` FROM `LogMailSent` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new LogMailSent((int)$row["LogMailSentId"]);
        }
        return $arr;
    }

    /**
     * @param LogMailSent $obj
     * @return LogMailSent|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(LogMailSent $obj): ?LogMailSent {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->LogMailSentId)) {
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
        if (isset($obj->UserId)) {
            $cols[] = "`UserId`";
            $vals[] = "\"".$d->filter($obj->UserId)."\"";
        }
        if (isset($obj->Recipient)) {
            $cols[] = "`Recipient`";
            $vals[] = "\"".$d->filter($obj->Recipient)."\"";
        }
        if (isset($obj->Subject)) {
            $cols[] = "`Subject`";
            $vals[] = "\"".$d->filter($obj->Subject)."\"";
        }
        if (isset($obj->Body)) {
            $cols[] = "`Body`";
            $vals[] = "\"".$d->filter($obj->Body)."\"";
        }
        if (isset($obj->Created)) {
            $cols[] = "`Created`";
            $vals[] = "\"".$d->filter($obj->Created)."\"";
        }
        $_q = "INSERT INTO `LogMailSent` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new LogMailSent((int)$id);
    }

}
