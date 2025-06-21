<?php

class TicketController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return Ticket[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketId` FROM `Ticket`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "TicketId";
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
            $r[] = new Ticket((int)$u["TicketId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Ticket|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Ticket
    {
        global $d;
        $_q = "SELECT `TicketId` FROM `Ticket` WHERE `TicketId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Ticket((int)$t["TicketId"]);
    }

    /**
     * @param string $guid
     * @return Ticket|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Ticket
    {
        global $d;
        $_q = "SELECT `TicketId` FROM `Ticket` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Ticket($t["TicketId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Ticket|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Ticket
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Ticket|null|Ticket[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketId", "Guid", "Secret1", "Secret2", "Secret3", "TicketNumber", "ConversationId", "StatusId", "MessengerName", "MessengerEmail", "Subject", "CategoryId", "AssigneeUserId", "DueDatetime", "CreatedDatetime", "UpdatedDatetime"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketId` FROM `Ticket` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new Ticket((int)$result["TicketId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Ticket((int)$row["TicketId"]);
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
        $allowed = ["TicketId", "Guid", "Secret1", "Secret2", "Secret3", "TicketNumber", "ConversationId", "StatusId", "MessengerName", "MessengerEmail", "Subject", "CategoryId", "AssigneeUserId", "DueDatetime", "CreatedDatetime", "UpdatedDatetime"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketId` FROM `Ticket` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return Ticket[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketId` FROM `Ticket` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Ticket((int)$row["TicketId"]);
        }
        return $arr;
    }

    /**
     * @param Ticket $obj
     * @return Ticket|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Ticket $obj): ?Ticket {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TicketId)) {
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
        if (isset($obj->TicketNumber)) {
            $cols[] = "`TicketNumber`";
            $vals[] = "\"".$d->filter($obj->TicketNumber)."\"";
        }
        if (isset($obj->ConversationId)) {
            $cols[] = "`ConversationId`";
            $vals[] = "\"".$d->filter($obj->ConversationId)."\"";
        }
        if (isset($obj->StatusId)) {
            $cols[] = "`StatusId`";
            $vals[] = "\"".$d->filter($obj->StatusId)."\"";
        }
        if (isset($obj->MessengerName)) {
            $cols[] = "`MessengerName`";
            $vals[] = "\"".$d->filter($obj->MessengerName)."\"";
        }
        if (isset($obj->MessengerEmail)) {
            $cols[] = "`MessengerEmail`";
            $vals[] = "\"".$d->filter($obj->MessengerEmail)."\"";
        }
        if (isset($obj->Subject)) {
            $cols[] = "`Subject`";
            $vals[] = "\"".$d->filter($obj->Subject)."\"";
        }
        if (isset($obj->CategoryId)) {
            $cols[] = "`CategoryId`";
            $vals[] = "\"".$d->filter($obj->CategoryId)."\"";
        }
        if (isset($obj->AssigneeUserId)) {
            $cols[] = "`AssigneeUserId`";
            $vals[] = "\"".$d->filter($obj->AssigneeUserId)."\"";
        }
        if (isset($obj->DueDatetime)) {
            $cols[] = "`DueDatetime`";
            $vals[] = "\"".$d->filter($obj->DueDatetime)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->UpdatedDatetime)) {
            $cols[] = "`UpdatedDatetime`";
            $vals[] = "\"".$d->filter($obj->UpdatedDatetime)."\"";
        }
        $_q = "INSERT INTO `Ticket` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Ticket((int)$id);
    }

}
