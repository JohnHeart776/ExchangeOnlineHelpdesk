<?php

class FileController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return File[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `FileId` FROM `File`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "FileId";
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
            $r[] = new File((int)$u["FileId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return File|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?File
    {
        global $d;
        $_q = "SELECT `FileId` FROM `File` WHERE `FileId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new File((int)$t["FileId"]);
    }

    /**
     * @param string $guid
     * @return File|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?File
    {
        global $d;
        $_q = "SELECT `FileId` FROM `File` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new File($t["FileId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return File|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?File
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return File|null|File[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["FileId", "Guid", "Secret1", "Secret2", "Secret3", "HashSha256", "CreatedDatetime", "Name", "Size", "Type", "Data"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `FileId` FROM `File` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new File((int)$result["FileId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new File((int)$row["FileId"]);
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
        $allowed = ["FileId", "Guid", "Secret1", "Secret2", "Secret3", "HashSha256", "CreatedDatetime", "Name", "Size", "Type", "Data"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `FileId` FROM `File` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return File[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `FileId` FROM `File` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new File((int)$row["FileId"]);
        }
        return $arr;
    }

    /**
     * @param File $obj
     * @return File|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(File $obj): ?File {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->FileId)) {
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
        if (isset($obj->HashSha256)) {
            $cols[] = "`HashSha256`";
            $vals[] = "\"".$d->filter($obj->HashSha256)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->Size)) {
            $cols[] = "`Size`";
            $vals[] = "\"".$d->filter($obj->Size)."\"";
        }
        if (isset($obj->Type)) {
            $cols[] = "`Type`";
            $vals[] = "\"".$d->filter($obj->Type)."\"";
        }
        if (isset($obj->Data)) {
            $cols[] = "`Data`";
            $vals[] = "\"".$d->filter($obj->Data)."\"";
        }
        $_q = "INSERT INTO `File` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new File((int)$id);
    }

}
