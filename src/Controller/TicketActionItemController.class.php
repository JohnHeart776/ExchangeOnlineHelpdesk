<?php

class TicketActionItemController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return TicketActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "TicketActionItemId";
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
            $r[] = new TicketActionItem((int)$u["TicketActionItemId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TicketActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TicketActionItem
    {
        global $d;
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem` WHERE `TicketActionItemId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketActionItem((int)$t["TicketActionItemId"]);
    }

    /**
     * @param string $guid
     * @return TicketActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TicketActionItem
    {
        global $d;
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketActionItem($t["TicketActionItemId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TicketActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TicketActionItem
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TicketActionItem|null|TicketActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketActionItemId", "Guid", "TicketId", "ActionItemId", "Title", "Description", "DueDatetime", "Comment", "Completed", "CompletedByUserId", "CompletedAt", "CreatedAt", "CreatedByUserId"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        // Optionales Limit anwenden, wenn nicht fetchOne
        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new TicketActionItem((int)$result["TicketActionItemId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TicketActionItem((int)$row["TicketActionItemId"]);
            }
            return $arr;
        }
    }

    /**
     * Überprüft, ob ein Element mit dem gegebenen Suchbegriff existiert.
     *
     * @param string $field
     * @param string $term
     * @return bool
     * @throws \Database\DatabaseQueryException
     */
    public static function exist(string $field, string $term): bool
    {
        global $d;
        $allowed = ["TicketActionItemId", "Guid", "TicketId", "ActionItemId", "Title", "Description", "DueDatetime", "Comment", "Completed", "CompletedByUserId", "CompletedAt", "CreatedAt", "CreatedByUserId"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return TicketActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketActionItemId` FROM `TicketActionItem` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TicketActionItem((int)$row["TicketActionItemId"]);
        }
        return $arr;
    }

    /**
     * @param TicketActionItem $obj
     * @return TicketActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TicketActionItem $obj): ?TicketActionItem {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TicketActionItemId)) {
            throw new Exception("Primary Key must be empty");
        }
        if (!empty($obj->Guid)) {
            throw new Exception("GUID must be empty");
        }
        // Setze CreatedAt und UpdatedAt mit dem dynamischen SQL-Wert NOW() falls vorhanden
        // Baue die INSERT-Abfrage auf. Alle Spalten außer dem Primary Key werden genutzt.
        $cols = [];
        $vals = [];
        $cols[] = "`Guid`";
        $vals[] = "UUID()";
        if (isset($obj->TicketId)) {
            $cols[] = "`TicketId`";
            $vals[] = "\"".$d->filter($obj->TicketId)."\"";
        }
        if (isset($obj->ActionItemId)) {
            $cols[] = "`ActionItemId`";
            $vals[] = "\"".$d->filter($obj->ActionItemId)."\"";
        }
        if (isset($obj->Title)) {
            $cols[] = "`Title`";
            $vals[] = "\"".$d->filter($obj->Title)."\"";
        }
        if (isset($obj->Description)) {
            $cols[] = "`Description`";
            $vals[] = "\"".$d->filter($obj->Description)."\"";
        }
        if (isset($obj->DueDatetime)) {
            $cols[] = "`DueDatetime`";
            $vals[] = "\"".$d->filter($obj->DueDatetime)."\"";
        }
        if (isset($obj->Comment)) {
            $cols[] = "`Comment`";
            $vals[] = "\"".$d->filter($obj->Comment)."\"";
        }
        if (isset($obj->Completed)) {
            $cols[] = "`Completed`";
            $vals[] = "\"".$d->filter($obj->Completed)."\"";
        }
        if (isset($obj->CompletedByUserId)) {
            $cols[] = "`CompletedByUserId`";
            $vals[] = "\"".$d->filter($obj->CompletedByUserId)."\"";
        }
        if (isset($obj->CompletedAt)) {
            $cols[] = "`CompletedAt`";
            $vals[] = "\"".$d->filter($obj->CompletedAt)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        if (isset($obj->CreatedByUserId)) {
            $cols[] = "`CreatedByUserId`";
            $vals[] = "\"".$d->filter($obj->CreatedByUserId)."\"";
        }
        $_q = "INSERT INTO `TicketActionItem` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TicketActionItem((int)$id);
    }

}
