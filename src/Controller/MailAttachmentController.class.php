<?php

class MailAttachmentController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment`";

        // Apply sorting only if $direction is set
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "MailAttachmentId";
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
            $r[] = new MailAttachment((int)$u["MailAttachmentId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return MailAttachment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?MailAttachment
    {
        global $d;
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `MailAttachmentId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MailAttachment((int)$t["MailAttachmentId"]);
    }

    /**
     * @param string $guid
     * @return MailAttachment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?MailAttachment
    {
        global $d;
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MailAttachment($t["MailAttachmentId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return MailAttachment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?MailAttachment
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optional limit (default: 0 = no limit)
     * @return MailAttachment|null|MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MailAttachmentId", "Guid", "AzureId", "Secret1", "Secret2", "Secret3", "MailId", "Name", "ContentType", "Size", "IsInline", "HashSha256", "Content", "TextRepresentation", "CreatedAt"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new MailAttachment((int)$result["MailAttachmentId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new MailAttachment((int)$row["MailAttachmentId"]);
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
        $allowed = ["MailAttachmentId", "Guid", "AzureId", "Secret1", "Secret2", "Secret3", "MailId", "Name", "ContentType", "Size", "IsInline", "HashSha256", "Content", "TextRepresentation", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Invalid search field: " . $field);
        }
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new MailAttachment((int)$row["MailAttachmentId"]);
        }
        return $arr;
    }

    /**
     * @param MailAttachment $obj
     * @return MailAttachment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(MailAttachment $obj): ?MailAttachment {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->MailAttachmentId)) {
            throw new Exception("Primary Key must be empty");
        }
        if (!empty($obj->Guid)) {
            throw new Exception("GUID must be empty");
        }
		// Set CreatedAt and UpdatedAt with the dynamic SQL value NOW() if available
		// Build the INSERT query. All columns except the Primary Key are used.
        $cols = [];
        $vals = [];
        $cols[] = "`Guid`";
        $vals[] = "UUID()";
        if (isset($obj->AzureId)) {
            $cols[] = "`AzureId`";
            $vals[] = "\"".$d->filter($obj->AzureId)."\"";
        }
        if (isset($obj->Secret1)) {
            $cols[] = "`Secret1`";
            $vals[] = "\"".$d->filter($obj->Secret1)."\"";
        }
        if (isset($obj->Secret2)) {
            $cols[] = "`Secret2`";
            $vals[] = "\"".$d->filter($obj->Secret2)."\"";
        }
        if (isset($obj->Secret3)) {
            $cols[] = "`Secret3`";
            $vals[] = "\"".$d->filter($obj->Secret3)."\"";
        }
        if (isset($obj->MailId)) {
            $cols[] = "`MailId`";
            $vals[] = "\"".$d->filter($obj->MailId)."\"";
        }
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->ContentType)) {
            $cols[] = "`ContentType`";
            $vals[] = "\"".$d->filter($obj->ContentType)."\"";
        }
        if (isset($obj->Size)) {
            $cols[] = "`Size`";
            $vals[] = "\"".$d->filter($obj->Size)."\"";
        }
        if (isset($obj->IsInline)) {
            $cols[] = "`IsInline`";
            $vals[] = "\"".$d->filter($obj->IsInline)."\"";
        }
        if (isset($obj->HashSha256)) {
            $cols[] = "`HashSha256`";
            $vals[] = "\"".$d->filter($obj->HashSha256)."\"";
        }
        if (isset($obj->Content)) {
            $cols[] = "`Content`";
            $vals[] = "\"".$d->filter($obj->Content)."\"";
        }
        if (isset($obj->TextRepresentation)) {
            $cols[] = "`TextRepresentation`";
            $vals[] = "\"".$d->filter($obj->TextRepresentation)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        $_q = "INSERT INTO `MailAttachment` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new MailAttachment((int)$id);
    }

}
