<?php

class TextReplaceController
{

    /**
     * Returns all objects from the table.
     *
     * @param int $limit Optional limit (default: 0 = no limit)
     * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
     * @param string|null $sortBy Optional column name for sorting, null = uses the first field
     * @return TextReplace[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TextReplaceId` FROM `TextReplace`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "TextReplaceId";
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
            $r[] = new TextReplace((int)$u["TextReplaceId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TextReplace|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TextReplace
    {
        global $d;
        $_q = "SELECT `TextReplaceId` FROM `TextReplace` WHERE `TextReplaceId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TextReplace((int)$t["TextReplaceId"]);
    }

    /**
     * @param string $guid
     * @return TextReplace|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TextReplace
    {
        global $d;
        $_q = "SELECT `TextReplaceId` FROM `TextReplace` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TextReplace($t["TextReplaceId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TextReplace|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TextReplace
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TextReplace|null|TextReplace[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TextReplaceId", "Guid", "Enabled", "SearchFor", "ReplaceBy"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Invalid search field: " . $field);
        }
        $_q = "SELECT `TextReplaceId` FROM `TextReplace` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TextReplace((int)$result["TextReplaceId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TextReplace((int)$row["TextReplaceId"]);
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
        $allowed = ["TextReplaceId", "Guid", "Enabled", "SearchFor", "ReplaceBy"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TextReplaceId` FROM `TextReplace` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Returns random objects from the table.
     *
     * @param int $amount Number of records to return (default: 1)
     * @return TextReplace[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `TextReplaceId` FROM `TextReplace` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TextReplace((int)$row["TextReplaceId"]);
        }
        return $arr;
    }

    /**
     * @param TextReplace $obj
     * @return TextReplace|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TextReplace $obj): ?TextReplace {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TextReplaceId)) {
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
        if (isset($obj->SearchFor)) {
            $cols[] = "`SearchFor`";
            $vals[] = "\"".$d->filter($obj->SearchFor)."\"";
        }
        if (isset($obj->ReplaceBy)) {
            $cols[] = "`ReplaceBy`";
            $vals[] = "\"".$d->filter($obj->ReplaceBy)."\"";
        }
        $_q = "INSERT INTO `TextReplace` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TextReplace((int)$id);
    }

}
