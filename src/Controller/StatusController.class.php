<?php

class StatusController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return Status[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `StatusId` FROM `Status`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // Falls kein Sortierfeld angegeben, benutze das erste Feld
            if ($sortBy === null) {
                $sortBy = "StatusId";
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
            $r[] = new Status((int)$u["StatusId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Status|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Status
    {
        global $d;
        $_q = "SELECT `StatusId` FROM `Status` WHERE `StatusId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Status((int)$t["StatusId"]);
    }

    /**
     * @param string $guid
     * @return Status|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Status
    {
        global $d;
        $_q = "SELECT `StatusId` FROM `Status` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Status($t["StatusId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Status|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Status
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Status|null|Status[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["StatusId", "Guid", "InternalName", "Color", "PublicName", "Icon", "IsOpen", "IsFinal", "IsDefault", "IsDefaultAssignedStatus", "IsDefaultWorkingStatus", "IsDetaultWaitingForCustomerStatus", "IsDefaultCustomerReplyStatus", "IsDefaultClosedStatus", "IsDefaultSolvedStatus", "SortOrder", "CustomerNotificationTemplateId", "AgentNotificationTemplateId", "CreatedAt", "UpdatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `StatusId` FROM `Status` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new Status((int)$result["StatusId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Status((int)$row["StatusId"]);
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
        $allowed = ["StatusId", "Guid", "InternalName", "Color", "PublicName", "Icon", "IsOpen", "IsFinal", "IsDefault", "IsDefaultAssignedStatus", "IsDefaultWorkingStatus", "IsDetaultWaitingForCustomerStatus", "IsDefaultCustomerReplyStatus", "IsDefaultClosedStatus", "IsDefaultSolvedStatus", "SortOrder", "CustomerNotificationTemplateId", "AgentNotificationTemplateId", "CreatedAt", "UpdatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `StatusId` FROM `Status` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
     * Liefert zufällige Objekte aus der Tabelle.
     *
     * @param int $amount Anzahl der zurückzugebenden Datensätze (Standard: 1)
     * @return Status[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // Mindestens 1
        $amount = max(1, $amount);
        $_q = "SELECT `StatusId` FROM `Status` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Status((int)$row["StatusId"]);
        }
        return $arr;
    }

    /**
     * @param Status $obj
     * @return Status|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Status $obj): ?Status {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->StatusId)) {
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
        if (isset($obj->InternalName)) {
            $cols[] = "`InternalName`";
            $vals[] = "\"".$d->filter($obj->InternalName)."\"";
        }
        if (isset($obj->Color)) {
            $cols[] = "`Color`";
            $vals[] = "\"".$d->filter($obj->Color)."\"";
        }
        if (isset($obj->PublicName)) {
            $cols[] = "`PublicName`";
            $vals[] = "\"".$d->filter($obj->PublicName)."\"";
        }
        if (isset($obj->Icon)) {
            $cols[] = "`Icon`";
            $vals[] = "\"".$d->filter($obj->Icon)."\"";
        }
        if (isset($obj->IsOpen)) {
            $cols[] = "`IsOpen`";
            $vals[] = "\"".$d->filter($obj->IsOpen)."\"";
        }
        if (isset($obj->IsFinal)) {
            $cols[] = "`IsFinal`";
            $vals[] = "\"".$d->filter($obj->IsFinal)."\"";
        }
        if (isset($obj->IsDefault)) {
            $cols[] = "`IsDefault`";
            $vals[] = "\"".$d->filter($obj->IsDefault)."\"";
        }
        if (isset($obj->IsDefaultAssignedStatus)) {
            $cols[] = "`IsDefaultAssignedStatus`";
            $vals[] = "\"".$d->filter($obj->IsDefaultAssignedStatus)."\"";
        }
        if (isset($obj->IsDefaultWorkingStatus)) {
            $cols[] = "`IsDefaultWorkingStatus`";
            $vals[] = "\"".$d->filter($obj->IsDefaultWorkingStatus)."\"";
        }
        if (isset($obj->IsDetaultWaitingForCustomerStatus)) {
            $cols[] = "`IsDetaultWaitingForCustomerStatus`";
            $vals[] = "\"".$d->filter($obj->IsDetaultWaitingForCustomerStatus)."\"";
        }
        if (isset($obj->IsDefaultCustomerReplyStatus)) {
            $cols[] = "`IsDefaultCustomerReplyStatus`";
            $vals[] = "\"".$d->filter($obj->IsDefaultCustomerReplyStatus)."\"";
        }
        if (isset($obj->IsDefaultClosedStatus)) {
            $cols[] = "`IsDefaultClosedStatus`";
            $vals[] = "\"".$d->filter($obj->IsDefaultClosedStatus)."\"";
        }
        if (isset($obj->IsDefaultSolvedStatus)) {
            $cols[] = "`IsDefaultSolvedStatus`";
            $vals[] = "\"".$d->filter($obj->IsDefaultSolvedStatus)."\"";
        }
        if (isset($obj->SortOrder)) {
            $cols[] = "`SortOrder`";
            $vals[] = "\"".$d->filter($obj->SortOrder)."\"";
        }
        if (isset($obj->CustomerNotificationTemplateId)) {
            $cols[] = "`CustomerNotificationTemplateId`";
            $vals[] = "\"".$d->filter($obj->CustomerNotificationTemplateId)."\"";
        }
        if (isset($obj->AgentNotificationTemplateId)) {
            $cols[] = "`AgentNotificationTemplateId`";
            $vals[] = "\"".$d->filter($obj->AgentNotificationTemplateId)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        $cols[] = "`UpdatedAt`";
        $vals[] = "NOW()";
        $_q = "INSERT INTO `Status` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Status((int)$id);
    }

}
