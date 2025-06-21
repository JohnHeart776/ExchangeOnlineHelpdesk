<?php

class TicketCommentController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return TicketComment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketCommentId` FROM `TicketComment`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "TicketCommentId";
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
            $r[] = new TicketComment((int)$u["TicketCommentId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TicketComment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TicketComment
    {
        global $d;
        $_q = "SELECT `TicketCommentId` FROM `TicketComment` WHERE `TicketCommentId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketComment((int)$t["TicketCommentId"]);
    }

    /**
     * @param string $guid
     * @return TicketComment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TicketComment
    {
        global $d;
        $_q = "SELECT `TicketCommentId` FROM `TicketComment` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketComment($t["TicketCommentId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TicketComment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TicketComment
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TicketComment|null|TicketComment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketCommentId", "Guid", "TicketId", "UserId", "AccessLevel", "CreatedDatetime", "LastUpdatedDatetime", "Facility", "TextType", "Text", "MailId", "GraphObject", "IsEditable"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketCommentId` FROM `TicketComment` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TicketComment((int)$result["TicketCommentId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TicketComment((int)$row["TicketCommentId"]);
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
        $allowed = ["TicketCommentId", "Guid", "TicketId", "UserId", "AccessLevel", "CreatedDatetime", "LastUpdatedDatetime", "Facility", "TextType", "Text", "MailId", "GraphObject", "IsEditable"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketCommentId` FROM `TicketComment` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return TicketComment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketCommentId` FROM `TicketComment` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TicketComment((int)$row["TicketCommentId"]);
        }
        return $arr;
    }

    /**
     * @param TicketComment $obj
     * @return TicketComment|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TicketComment $obj): ?TicketComment {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TicketCommentId)) {
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
        if (isset($obj->UserId)) {
            $cols[] = "`UserId`";
            $vals[] = "\"".$d->filter($obj->UserId)."\"";
        }
        if (isset($obj->AccessLevel)) {
            $cols[] = "`AccessLevel`";
            $vals[] = "\"".$d->filter($obj->AccessLevel)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->LastUpdatedDatetime)) {
            $cols[] = "`LastUpdatedDatetime`";
            $vals[] = "\"".$d->filter($obj->LastUpdatedDatetime)."\"";
        }
        if (isset($obj->Facility)) {
            $cols[] = "`Facility`";
            $vals[] = "\"".$d->filter($obj->Facility)."\"";
        }
        if (isset($obj->TextType)) {
            $cols[] = "`TextType`";
            $vals[] = "\"".$d->filter($obj->TextType)."\"";
        }
        if (isset($obj->Text)) {
            $cols[] = "`Text`";
            $vals[] = "\"".$d->filter($obj->Text)."\"";
        }
        if (isset($obj->MailId)) {
            $cols[] = "`MailId`";
            $vals[] = "\"".$d->filter($obj->MailId)."\"";
        }
        if (isset($obj->GraphObject)) {
            $cols[] = "`GraphObject`";
            $vals[] = "\"".$d->filter($obj->GraphObject)."\"";
        }
        if (isset($obj->IsEditable)) {
            $cols[] = "`IsEditable`";
            $vals[] = "\"".$d->filter($obj->IsEditable)."\"";
        }
        $_q = "INSERT INTO `TicketComment` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TicketComment((int)$id);
    }

}
