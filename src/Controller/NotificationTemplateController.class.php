<?php

class NotificationTemplateController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return NotificationTemplate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "NotificationTemplateId";
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
            $r[] = new NotificationTemplate((int)$u["NotificationTemplateId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return NotificationTemplate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?NotificationTemplate
    {
        global $d;
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate` WHERE `NotificationTemplateId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new NotificationTemplate((int)$t["NotificationTemplateId"]);
    }

    /**
     * @param string $guid
     * @return NotificationTemplate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?NotificationTemplate
    {
        global $d;
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new NotificationTemplate($t["NotificationTemplateId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return NotificationTemplate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?NotificationTemplate
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return NotificationTemplate|null|NotificationTemplate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["NotificationTemplateId", "Guid", "Enabled", "InternalName", "Name", "MailSubject", "MailText"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new NotificationTemplate((int)$result["NotificationTemplateId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new NotificationTemplate((int)$row["NotificationTemplateId"]);
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
        $allowed = ["NotificationTemplateId", "Guid", "Enabled", "InternalName", "Name", "MailSubject", "MailText"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return NotificationTemplate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `NotificationTemplateId` FROM `NotificationTemplate` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new NotificationTemplate((int)$row["NotificationTemplateId"]);
        }
        return $arr;
    }

    /**
     * @param NotificationTemplate $obj
     * @return NotificationTemplate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(NotificationTemplate $obj): ?NotificationTemplate {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->NotificationTemplateId)) {
            throw new Exception("Primary Key must be empty");
        }
        if (!empty($obj->Guid)) {
            throw new Exception("GUID must be empty");
        }
        // Set CreatedAt and UpdatedAt with dynamic SQL value NOW() if available
        // Baue die INSERT-Abfrage auf. Alle Spalten außer dem Primary Key werden genutzt.
        $cols = [];
        $vals = [];
        $cols[] = "`Guid`";
        $vals[] = "UUID()";
        if (isset($obj->Enabled)) {
            $cols[] = "`Enabled`";
            $vals[] = "\"".$d->filter($obj->Enabled)."\"";
        }
        if (isset($obj->InternalName)) {
            $cols[] = "`InternalName`";
            $vals[] = "\"".$d->filter($obj->InternalName)."\"";
        }
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->MailSubject)) {
            $cols[] = "`MailSubject`";
            $vals[] = "\"".$d->filter($obj->MailSubject)."\"";
        }
        if (isset($obj->MailText)) {
            $cols[] = "`MailText`";
            $vals[] = "\"".$d->filter($obj->MailText)."\"";
        }
        $_q = "INSERT INTO `NotificationTemplate` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new NotificationTemplate((int)$id);
    }

}
