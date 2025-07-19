<?php

class OrganizationUserController
{

    /**
     * Liefert alle Objekte aus der Tabelle.
     *
	 * @param int $limit Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optionaler Sortiermodus ("ASC" oder "DESC"), null = keine Sortierung
     * @param string|null $sortBy Optionaler Spaltenname für die Sortierung, null = verwendet das erste Feld
     * @return OrganizationUser[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser`";

		// Apply sorting only if $direction is set
		if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "OrganizationUserId";
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
            $r[] = new OrganizationUser((int)$u["OrganizationUserId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return OrganizationUser|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?OrganizationUser
    {
        global $d;
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser` WHERE `OrganizationUserId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new OrganizationUser((int)$t["OrganizationUserId"]);
    }

    /**
     * @param string $guid
     * @return OrganizationUser|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?OrganizationUser
    {
        global $d;
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new OrganizationUser($t["OrganizationUserId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return OrganizationUser|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?OrganizationUser
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return OrganizationUser|null|OrganizationUser[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["OrganizationUserId", "Guid", "AzureObjectId", "DisplayName", "UserPrincipalName", "Mail", "GivenName", "Surname", "JobTitle", "Department", "MobilePhone", "OfficeLocation", "CompanyName", "BusinessPhones", "AccountEnabled", "EmployeeId", "SamAccountName", "Photo", "CreatedAt"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new OrganizationUser((int)$result["OrganizationUserId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new OrganizationUser((int)$row["OrganizationUserId"]);
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
        $allowed = ["OrganizationUserId", "Guid", "AzureObjectId", "DisplayName", "UserPrincipalName", "Mail", "GivenName", "Surname", "JobTitle", "Department", "MobilePhone", "OfficeLocation", "CompanyName", "BusinessPhones", "AccountEnabled", "EmployeeId", "SamAccountName", "Photo", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return OrganizationUser[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `OrganizationUserId` FROM `OrganizationUser` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new OrganizationUser((int)$row["OrganizationUserId"]);
        }
        return $arr;
    }

    /**
     * @param OrganizationUser $obj
     * @return OrganizationUser|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(OrganizationUser $obj): ?OrganizationUser {
        global $d;
		// Check if the Primary Key (first field) is empty
		if (!empty($obj->OrganizationUserId)) {
			throw new Exception("Primary Key must be empty");
		}
		if (!empty($obj->Guid)) {
			throw new Exception("GUID must be empty");
		}
		// Set CreatedAt and UpdatedAt with dynamic SQL value NOW() if available
		// Build the INSERT query. All columns except the Primary Key are used.
		$cols = [];
        $vals = [];
        $cols[] = "`Guid`";
        $vals[] = "UUID()";
        if (isset($obj->AzureObjectId)) {
            $cols[] = "`AzureObjectId`";
            $vals[] = "\"".$d->filter($obj->AzureObjectId)."\"";
        }
        if (isset($obj->DisplayName)) {
            $cols[] = "`DisplayName`";
            $vals[] = "\"".$d->filter($obj->DisplayName)."\"";
        }
        if (isset($obj->UserPrincipalName)) {
            $cols[] = "`UserPrincipalName`";
            $vals[] = "\"".$d->filter($obj->UserPrincipalName)."\"";
        }
        if (isset($obj->Mail)) {
            $cols[] = "`Mail`";
            $vals[] = "\"".$d->filter($obj->Mail)."\"";
        }
        if (isset($obj->GivenName)) {
            $cols[] = "`GivenName`";
            $vals[] = "\"".$d->filter($obj->GivenName)."\"";
        }
        if (isset($obj->Surname)) {
            $cols[] = "`Surname`";
            $vals[] = "\"".$d->filter($obj->Surname)."\"";
        }
        if (isset($obj->JobTitle)) {
            $cols[] = "`JobTitle`";
            $vals[] = "\"".$d->filter($obj->JobTitle)."\"";
        }
        if (isset($obj->Department)) {
            $cols[] = "`Department`";
            $vals[] = "\"".$d->filter($obj->Department)."\"";
        }
        if (isset($obj->MobilePhone)) {
            $cols[] = "`MobilePhone`";
            $vals[] = "\"".$d->filter($obj->MobilePhone)."\"";
        }
        if (isset($obj->OfficeLocation)) {
            $cols[] = "`OfficeLocation`";
            $vals[] = "\"".$d->filter($obj->OfficeLocation)."\"";
        }
        if (isset($obj->CompanyName)) {
            $cols[] = "`CompanyName`";
            $vals[] = "\"".$d->filter($obj->CompanyName)."\"";
        }
        if (isset($obj->BusinessPhones)) {
            $cols[] = "`BusinessPhones`";
            $vals[] = "\"".$d->filter($obj->BusinessPhones)."\"";
        }
        if (isset($obj->AccountEnabled)) {
            $cols[] = "`AccountEnabled`";
            $vals[] = "\"".$d->filter($obj->AccountEnabled)."\"";
        }
        if (isset($obj->EmployeeId)) {
            $cols[] = "`EmployeeId`";
            $vals[] = "\"".$d->filter($obj->EmployeeId)."\"";
        }
        if (isset($obj->SamAccountName)) {
            $cols[] = "`SamAccountName`";
            $vals[] = "\"".$d->filter($obj->SamAccountName)."\"";
        }
        if (isset($obj->Photo)) {
            $cols[] = "`Photo`";
            $vals[] = "\"".$d->filter($obj->Photo)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        $_q = "INSERT INTO `OrganizationUser` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new OrganizationUser((int)$id);
    }

}
