<?php

class CategorySuggestionController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return CategorySuggestion[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "CategorySuggestionId";
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
            $r[] = new CategorySuggestion((int)$u["CategorySuggestionId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return CategorySuggestion|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?CategorySuggestion
    {
        global $d;
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion` WHERE `CategorySuggestionId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new CategorySuggestion((int)$t["CategorySuggestionId"]);
    }

    /**
     * @param string $guid
     * @return CategorySuggestion|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?CategorySuggestion
    {
        global $d;
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new CategorySuggestion($t["CategorySuggestionId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return CategorySuggestion|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?CategorySuggestion
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return CategorySuggestion|null|CategorySuggestion[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["CategorySuggestionId", "Guid", "Enabled", "Priority", "Filter", "CategoryId", "AutoClose"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new CategorySuggestion((int)$result["CategorySuggestionId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new CategorySuggestion((int)$row["CategorySuggestionId"]);
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
        $allowed = ["CategorySuggestionId", "Guid", "Enabled", "Priority", "Filter", "CategoryId", "AutoClose"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return CategorySuggestion[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `CategorySuggestionId` FROM `CategorySuggestion` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new CategorySuggestion((int)$row["CategorySuggestionId"]);
        }
        return $arr;
    }

    /**
     * @param CategorySuggestion $obj
     * @return CategorySuggestion|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(CategorySuggestion $obj): ?CategorySuggestion {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->CategorySuggestionId)) {
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
        if (isset($obj->Enabled)) {
            $cols[] = "`Enabled`";
            $vals[] = "\"".$d->filter($obj->Enabled)."\"";
        }
        if (isset($obj->Priority)) {
            $cols[] = "`Priority`";
            $vals[] = "\"".$d->filter($obj->Priority)."\"";
        }
        if (isset($obj->Filter)) {
            $cols[] = "`Filter`";
            $vals[] = "\"".$d->filter($obj->Filter)."\"";
        }
        if (isset($obj->CategoryId)) {
            $cols[] = "`CategoryId`";
            $vals[] = "\"".$d->filter($obj->CategoryId)."\"";
        }
        if (isset($obj->AutoClose)) {
            $cols[] = "`AutoClose`";
            $vals[] = "\"".$d->filter($obj->AutoClose)."\"";
        }
        $_q = "INSERT INTO `CategorySuggestion` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new CategorySuggestion((int)$id);
    }

}
