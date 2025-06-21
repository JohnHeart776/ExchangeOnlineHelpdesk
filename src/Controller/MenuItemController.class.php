<?php

class MenuItemController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return MenuItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MenuItemId` FROM `MenuItem`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "MenuItemId";
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
            $r[] = new MenuItem((int)$u["MenuItemId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return MenuItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?MenuItem
    {
        global $d;
        $_q = "SELECT `MenuItemId` FROM `MenuItem` WHERE `MenuItemId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MenuItem((int)$t["MenuItemId"]);
    }

    /**
     * @param string $guid
     * @return MenuItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?MenuItem
    {
        global $d;
        $_q = "SELECT `MenuItemId` FROM `MenuItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new MenuItem($t["MenuItemId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return MenuItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?MenuItem
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return MenuItem|null|MenuItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MenuItemId", "Guid", "MenuId", "ParentMenuItemId", "SortOrder", "Enabled", "Title", "Link", "Icon", "Color", "ImageFileId", "requireIsUser", "requireIsAgent", "requireIsAdmin"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MenuItemId` FROM `MenuItem` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new MenuItem((int)$result["MenuItemId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new MenuItem((int)$row["MenuItemId"]);
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
        $allowed = ["MenuItemId", "Guid", "MenuId", "ParentMenuItemId", "SortOrder", "Enabled", "Title", "Link", "Icon", "Color", "ImageFileId", "requireIsUser", "requireIsAgent", "requireIsAdmin"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MenuItemId` FROM `MenuItem` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return MenuItem[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `MenuItemId` FROM `MenuItem` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new MenuItem((int)$row["MenuItemId"]);
        }
        return $arr;
    }

    /**
     * @param MenuItem $obj
     * @return MenuItem|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(MenuItem $obj): ?MenuItem {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->MenuItemId)) {
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
        if (isset($obj->MenuId)) {
            $cols[] = "`MenuId`";
            $vals[] = "\"".$d->filter($obj->MenuId)."\"";
        }
        if (isset($obj->ParentMenuItemId)) {
            $cols[] = "`ParentMenuItemId`";
            $vals[] = "\"".$d->filter($obj->ParentMenuItemId)."\"";
        }
        if (isset($obj->SortOrder)) {
            $cols[] = "`SortOrder`";
            $vals[] = "\"".$d->filter($obj->SortOrder)."\"";
        }
        if (isset($obj->Enabled)) {
            $cols[] = "`Enabled`";
            $vals[] = "\"".$d->filter($obj->Enabled)."\"";
        }
        if (isset($obj->Title)) {
            $cols[] = "`Title`";
            $vals[] = "\"".$d->filter($obj->Title)."\"";
        }
        if (isset($obj->Link)) {
            $cols[] = "`Link`";
            $vals[] = "\"".$d->filter($obj->Link)."\"";
        }
        if (isset($obj->Icon)) {
            $cols[] = "`Icon`";
            $vals[] = "\"".$d->filter($obj->Icon)."\"";
        }
        if (isset($obj->Color)) {
            $cols[] = "`Color`";
            $vals[] = "\"".$d->filter($obj->Color)."\"";
        }
        if (isset($obj->ImageFileId)) {
            $cols[] = "`ImageFileId`";
            $vals[] = "\"".$d->filter($obj->ImageFileId)."\"";
        }
        if (isset($obj->requireIsUser)) {
            $cols[] = "`requireIsUser`";
            $vals[] = "\"".$d->filter($obj->requireIsUser)."\"";
        }
        if (isset($obj->requireIsAgent)) {
            $cols[] = "`requireIsAgent`";
            $vals[] = "\"".$d->filter($obj->requireIsAgent)."\"";
        }
        if (isset($obj->requireIsAdmin)) {
            $cols[] = "`requireIsAdmin`";
            $vals[] = "\"".$d->filter($obj->requireIsAdmin)."\"";
        }
        $_q = "INSERT INTO `MenuItem` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new MenuItem((int)$id);
    }

}
