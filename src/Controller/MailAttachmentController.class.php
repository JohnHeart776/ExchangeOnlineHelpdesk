<?php

class MailAttachmentController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
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
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return MailAttachment|null|MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MailAttachmentId", "Guid", "AzureId", "Secret1", "Secret2", "Secret3", "MailId", "Name", "ContentType", "Size", "IsInline", "HashSha256", "Content", "TextRepresentation", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
        $allowed = ["MailAttachmentId", "Guid", "AzureId", "Secret1", "Secret2", "Secret3", "MailId", "Name", "ContentType", "Size", "IsInline", "HashSha256", "Content", "TextRepresentation", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MailAttachmentId` FROM `MailAttachment` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return MailAttachment[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
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
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->MailAttachmentId)) {
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
