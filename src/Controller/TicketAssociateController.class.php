<?php

class TicketAssociateController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return TicketAssociate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "TicketAssociateId";
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
            $r[] = new TicketAssociate((int)$u["TicketAssociateId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return TicketAssociate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?TicketAssociate
    {
        global $d;
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate` WHERE `TicketAssociateId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketAssociate((int)$t["TicketAssociateId"]);
    }

    /**
     * @param string $guid
     * @return TicketAssociate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?TicketAssociate
    {
        global $d;
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new TicketAssociate($t["TicketAssociateId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return TicketAssociate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?TicketAssociate
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return TicketAssociate|null|TicketAssociate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["TicketAssociateId", "Guid", "TicketId", "OrganizationUserId"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new TicketAssociate((int)$result["TicketAssociateId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new TicketAssociate((int)$row["TicketAssociateId"]);
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
        $allowed = ["TicketAssociateId", "Guid", "TicketId", "OrganizationUserId"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return TicketAssociate[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `TicketAssociateId` FROM `TicketAssociate` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new TicketAssociate((int)$row["TicketAssociateId"]);
        }
        return $arr;
    }

    /**
     * @param TicketAssociate $obj
     * @return TicketAssociate|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(TicketAssociate $obj): ?TicketAssociate {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->TicketAssociateId)) {
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
        if (isset($obj->OrganizationUserId)) {
            $cols[] = "`OrganizationUserId`";
            $vals[] = "\"".$d->filter($obj->OrganizationUserId)."\"";
        }
        $_q = "INSERT INTO `TicketAssociate` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new TicketAssociate((int)$id);
    }

}
