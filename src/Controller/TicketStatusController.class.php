<?php

class TicketStatusController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return TicketStatus[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "TicketStatusId";
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
            $r[] = new TicketStatus((int)$u["TicketStatusId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TicketStatus|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TicketStatus
    {
        global $d;
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus` WHERE `TicketStatusId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketStatus((int)$t["TicketStatusId"]);
    }

    /**
     * @param string $guid
     * @return TicketStatus|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TicketStatus
    {
        global $d;
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketStatus($t["TicketStatusId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TicketStatus|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TicketStatus
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TicketStatus|null|TicketStatus[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketStatusId", "Guid", "CreatedDatetime", "TicketId", "OldStatusId", "OldStatusIdIsFinal", "NewStatusId", "NewStatusIdIsFinal", "UserId", "Comment"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TicketStatus((int)$result["TicketStatusId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TicketStatus((int)$row["TicketStatusId"]);
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
        $allowed = ["TicketStatusId", "Guid", "CreatedDatetime", "TicketId", "OldStatusId", "OldStatusIdIsFinal", "NewStatusId", "NewStatusIdIsFinal", "UserId", "Comment"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return TicketStatus[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketStatusId` FROM `TicketStatus` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TicketStatus((int)$row["TicketStatusId"]);
        }
        return $arr;
    }

    /**
     * @param TicketStatus $obj
     * @return TicketStatus|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TicketStatus $obj): ?TicketStatus {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TicketStatusId)) {
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
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->TicketId)) {
            $cols[] = "`TicketId`";
            $vals[] = "\"".$d->filter($obj->TicketId)."\"";
        }
        if (isset($obj->OldStatusId)) {
            $cols[] = "`OldStatusId`";
            $vals[] = "\"".$d->filter($obj->OldStatusId)."\"";
        }
        if (isset($obj->OldStatusIdIsFinal)) {
            $cols[] = "`OldStatusIdIsFinal`";
            $vals[] = "\"".$d->filter($obj->OldStatusIdIsFinal)."\"";
        }
        if (isset($obj->NewStatusId)) {
            $cols[] = "`NewStatusId`";
            $vals[] = "\"".$d->filter($obj->NewStatusId)."\"";
        }
        if (isset($obj->NewStatusIdIsFinal)) {
            $cols[] = "`NewStatusIdIsFinal`";
            $vals[] = "\"".$d->filter($obj->NewStatusIdIsFinal)."\"";
        }
        if (isset($obj->UserId)) {
            $cols[] = "`UserId`";
            $vals[] = "\"".$d->filter($obj->UserId)."\"";
        }
        if (isset($obj->Comment)) {
            $cols[] = "`Comment`";
            $vals[] = "\"".$d->filter($obj->Comment)."\"";
        }
        $_q = "INSERT INTO `TicketStatus` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TicketStatus((int)$id);
    }

}
