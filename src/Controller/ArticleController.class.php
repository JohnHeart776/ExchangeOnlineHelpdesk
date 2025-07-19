<?php

class ArticleController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (Default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses first field
	 * @return Article[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `ArticleId` FROM `Article`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "ArticleId";
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
            $r[] = new Article((int)$u["ArticleId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Article|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Article
    {
        global $d;
        $_q = "SELECT `ArticleId` FROM `Article` WHERE `ArticleId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Article((int)$t["ArticleId"]);
    }

    /**
     * @param string $guid
     * @return Article|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Article
    {
        global $d;
        $_q = "SELECT `ArticleId` FROM `Article` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Article($t["ArticleId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Article|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Article
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Article|null|Article[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["ArticleId", "Guid", "Published", "AccessLevel", "CreatedDatetime", "UpdatedAtDatetime", "Slug", "Title", "Content"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `ArticleId` FROM `Article` WHERE `$field` LIKE \"".$d->filter($term)."\"";

        if ($limit > 0 && !$fetchOne) {
            $_q .= " LIMIT " . $d->filter($limit);
        }

        if ($fetchOne) {
            $_q .= " LIMIT 1";
            $result = $d->get($_q, true);
            if (empty($result)) {
                return null;
            }
            return new Article((int)$result["ArticleId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Article((int)$row["ArticleId"]);
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
        $allowed = ["ArticleId", "Guid", "Published", "AccessLevel", "CreatedDatetime", "UpdatedAtDatetime", "Slug", "Title", "Content"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `ArticleId` FROM `Article` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (Default: 1)
	 * @return Article[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
		// At least 1
		$amount = max(1, $amount);
        $_q = "SELECT `ArticleId` FROM `Article` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Article((int)$row["ArticleId"]);
        }
        return $arr;
    }

    /**
     * @param Article $obj
     * @return Article|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Article $obj): ?Article {
        global $d;
		// Check if Primary Key (first field) is empty
		if (!empty($obj->ArticleId)) {
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
        if (isset($obj->Published)) {
            $cols[] = "`Published`";
            $vals[] = "\"".$d->filter($obj->Published)."\"";
        }
        if (isset($obj->AccessLevel)) {
            $cols[] = "`AccessLevel`";
            $vals[] = "\"".$d->filter($obj->AccessLevel)."\"";
        }
        if (isset($obj->CreatedDatetime)) {
            $cols[] = "`CreatedDatetime`";
            $vals[] = "\"".$d->filter($obj->CreatedDatetime)."\"";
        }
        if (isset($obj->UpdatedAtDatetime)) {
            $cols[] = "`UpdatedAtDatetime`";
            $vals[] = "\"".$d->filter($obj->UpdatedAtDatetime)."\"";
        }
        if (isset($obj->Slug)) {
            $cols[] = "`Slug`";
            $vals[] = "\"".$d->filter($obj->Slug)."\"";
        }
        if (isset($obj->Title)) {
            $cols[] = "`Title`";
            $vals[] = "\"".$d->filter($obj->Title)."\"";
        }
        if (isset($obj->Content)) {
            $cols[] = "`Content`";
            $vals[] = "\"".$d->filter($obj->Content)."\"";
        }
        $_q = "INSERT INTO `Article` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Article((int)$id);
    }

}
