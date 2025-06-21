<?php

class ActionItemController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return ActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `ActionItemId` FROM `ActionItem`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "ActionItemId";
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
            $r[] = new ActionItem((int)$u["ActionItemId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return ActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?ActionItem
    {
        global $d;
        $_q = "SELECT `ActionItemId` FROM `ActionItem` WHERE `ActionItemId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new ActionItem((int)$t["ActionItemId"]);
    }

    /**
     * @param string $guid
     * @return ActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?ActionItem
    {
        global $d;
        $_q = "SELECT `ActionItemId` FROM `ActionItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new ActionItem($t["ActionItemId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return ActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?ActionItem
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return ActionItem|null|ActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["ActionItemId", "Guid", "ActionGroupId", "Title", "Description", "IsOptional", "SortOrder"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `ActionItemId` FROM `ActionItem` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new ActionItem((int)$result["ActionItemId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new ActionItem((int)$row["ActionItemId"]);
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
        $allowed = ["ActionItemId", "Guid", "ActionGroupId", "Title", "Description", "IsOptional", "SortOrder"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `ActionItemId` FROM `ActionItem` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return ActionItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `ActionItemId` FROM `ActionItem` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new ActionItem((int)$row["ActionItemId"]);
        }
        return $arr;
    }

    /**
     * @param ActionItem $obj
     * @return ActionItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(ActionItem $obj): ?ActionItem {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->ActionItemId)) {
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
        if (isset($obj->ActionGroupId)) {
            $cols[] = "`ActionGroupId`";
            $vals[] = "\"".$d->filter($obj->ActionGroupId)."\"";
        }
        if (isset($obj->Title)) {
            $cols[] = "`Title`";
            $vals[] = "\"".$d->filter($obj->Title)."\"";
        }
        if (isset($obj->Description)) {
            $cols[] = "`Description`";
            $vals[] = "\"".$d->filter($obj->Description)."\"";
        }
        if (isset($obj->IsOptional)) {
            $cols[] = "`IsOptional`";
            $vals[] = "\"".$d->filter($obj->IsOptional)."\"";
        }
        if (isset($obj->SortOrder)) {
            $cols[] = "`SortOrder`";
            $vals[] = "\"".$d->filter($obj->SortOrder)."\"";
        }
        $_q = "INSERT INTO `ActionItem` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new ActionItem((int)$id);
    }

}
