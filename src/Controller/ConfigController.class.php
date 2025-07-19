<?php

class ConfigController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return Config[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `ConfigId` FROM `Config`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "ConfigId";
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
            $r[] = new Config((int)$u["ConfigId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Config|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Config
    {
        global $d;
        $_q = "SELECT `ConfigId` FROM `Config` WHERE `ConfigId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Config((int)$t["ConfigId"]);
    }

    /**
     * @param string $guid
     * @return Config|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Config
    {
        global $d;
        $_q = "SELECT `ConfigId` FROM `Config` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Config($t["ConfigId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Config|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Config
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Config|null|Config[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["ConfigId", "Guid", "Name", "Value"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `ConfigId` FROM `Config` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new Config((int)$result["ConfigId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Config((int)$row["ConfigId"]);
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
        $allowed = ["ConfigId", "Guid", "Name", "Value"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `ConfigId` FROM `Config` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return Config[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `ConfigId` FROM `Config` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Config((int)$row["ConfigId"]);
        }
        return $arr;
    }

    /**
     * @param Config $obj
     * @return Config|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Config $obj): ?Config {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->ConfigId)) {
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
        if (isset($obj->Name)) {
            $cols[] = "`Name`";
            $vals[] = "\"".$d->filter($obj->Name)."\"";
        }
        if (isset($obj->Value)) {
            $cols[] = "`Value`";
            $vals[] = "\"".$d->filter($obj->Value)."\"";
        }
        $_q = "INSERT INTO `Config` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Config((int)$id);
    }

}
