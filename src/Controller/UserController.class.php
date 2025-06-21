<?php

class UserController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return User[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `UserId` FROM `User`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "UserId";
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
            $r[] = new User((int)$u["UserId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return User|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?User
    {
        global $d;
        $_q = "SELECT `UserId` FROM `User` WHERE `UserId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new User((int)$t["UserId"]);
    }

    /**
     * @param string $guid
     * @return User|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?User
    {
        global $d;
        $_q = "SELECT `UserId` FROM `User` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new User($t["UserId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return User|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?User
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return User|null|User[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["UserId", "Guid", "Enabled", "TenantId", "AzureObjectId", "Upn", "DisplayName", "Name", "Surname", "Title", "Mail", "Telephone", "OfficeLocation", "CompanyName", "MobilePhone", "BusinessPhones", "AccountEnabled", "UserRole", "LastLogin"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `UserId` FROM `User` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new User((int)$result["UserId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new User((int)$row["UserId"]);
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
        $allowed = ["UserId", "Guid", "Enabled", "TenantId", "AzureObjectId", "Upn", "DisplayName", "Name", "Surname", "Title", "Mail", "Telephone", "OfficeLocation", "CompanyName", "MobilePhone", "BusinessPhones", "AccountEnabled", "UserRole", "LastLogin"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `UserId` FROM `User` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return User[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `UserId` FROM `User` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new User((int)$row["UserId"]);
        }
        return $arr;
    }

    /**
     * @param User $obj
     * @return User|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(User $obj): ?User {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->UserId)) {
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
        if (isset($obj->TenantId)) {
            $cols[] = "`TenantId`";
            $vals[] = "\"".$d->filter($obj->TenantId)."\"";
        }
        if (isset($obj->AzureObjectId)) {
            $cols[] = "`AzureObjectId`";
            $vals[] = "\"".$d->filter($obj->AzureObjectId)."\"";
        }
        if (isset($obj->Upn)) {
            $cols[] = "`Upn`";
            $vals[] = "\"".$d->filter($obj->Upn)."\"";
        }
        if (isset($obj->DisplayName)) {
            $cols[] = "`DisplayName`";
            $vals[] = "\"".$d->filter($obj->DisplayName)."\"";
        }
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->Surname)) {
            $cols[] = "`Surname`";
            $vals[] = "\"".$d->filter($obj->Surname)."\"";
        }
        if (isset($obj->Title)) {
            $cols[] = "`Title`";
            $vals[] = "\"".$d->filter($obj->Title)."\"";
        }
        if (isset($obj->Mail)) {
            $cols[] = "`Mail`";
            $vals[] = "\"".$d->filter($obj->Mail)."\"";
        }
        if (isset($obj->Telephone)) {
            $cols[] = "`Telephone`";
            $vals[] = "\"".$d->filter($obj->Telephone)."\"";
        }
        if (isset($obj->OfficeLocation)) {
            $cols[] = "`OfficeLocation`";
            $vals[] = "\"".$d->filter($obj->OfficeLocation)."\"";
        }
        if (isset($obj->CompanyName)) {
            $cols[] = "`CompanyName`";
            $vals[] = "\"".$d->filter($obj->CompanyName)."\"";
        }
        if (isset($obj->MobilePhone)) {
            $cols[] = "`MobilePhone`";
            $vals[] = "\"".$d->filter($obj->MobilePhone)."\"";
        }
        if (isset($obj->BusinessPhones)) {
            $cols[] = "`BusinessPhones`";
            $vals[] = "\"".$d->filter($obj->BusinessPhones)."\"";
        }
        if (isset($obj->AccountEnabled)) {
            $cols[] = "`AccountEnabled`";
            $vals[] = "\"".$d->filter($obj->AccountEnabled)."\"";
        }
        if (isset($obj->UserRole)) {
            $cols[] = "`UserRole`";
            $vals[] = "\"".$d->filter($obj->UserRole)."\"";
        }
        if (isset($obj->LastLogin)) {
            $cols[] = "`LastLogin`";
            $vals[] = "\"".$d->filter($obj->LastLogin)."\"";
        }
        $_q = "INSERT INTO `User` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new User((int)$id);
    }

}
