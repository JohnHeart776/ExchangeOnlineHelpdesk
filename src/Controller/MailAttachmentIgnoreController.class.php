<?php

class MailAttachmentIgnoreController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return MailAttachmentIgnore[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore`";

        // Apply sorting only if $direction is set
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "MailAttachmentIgnoreId";
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
            $r[] = new MailAttachmentIgnore((int)$u["MailAttachmentIgnoreId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return MailAttachmentIgnore|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?MailAttachmentIgnore
    {
        global $d;
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore` WHERE `MailAttachmentIgnoreId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MailAttachmentIgnore((int)$t["MailAttachmentIgnoreId"]);
    }

    /**
     * @param string $guid
     * @return MailAttachmentIgnore|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?MailAttachmentIgnore
    {
        global $d;
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MailAttachmentIgnore($t["MailAttachmentIgnoreId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return MailAttachmentIgnore|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?MailAttachmentIgnore
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optional limit (default: 0 = no limit)
     * @return MailAttachmentIgnore|null|MailAttachmentIgnore[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MailAttachmentIgnoreId", "Guid", "Enabled", "HashSha256", "CreatedAt"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new MailAttachmentIgnore((int)$result["MailAttachmentIgnoreId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new MailAttachmentIgnore((int)$row["MailAttachmentIgnoreId"]);
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
        $allowed = ["MailAttachmentIgnoreId", "Guid", "Enabled", "HashSha256", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Invalid search field: " . $field);
        }
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return MailAttachmentIgnore[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `MailAttachmentIgnoreId` FROM `MailAttachmentIgnore` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new MailAttachmentIgnore((int)$row["MailAttachmentIgnoreId"]);
        }
        return $arr;
    }

    /**
     * @param MailAttachmentIgnore $obj
     * @return MailAttachmentIgnore|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(MailAttachmentIgnore $obj): ?MailAttachmentIgnore {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->MailAttachmentIgnoreId)) {
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
        if (isset($obj->HashSha256)) {
            $cols[] = "`HashSha256`";
            $vals[] = "\"".$d->filter($obj->HashSha256)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        $_q = "INSERT INTO `MailAttachmentIgnore` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new MailAttachmentIgnore((int)$id);
    }

}
