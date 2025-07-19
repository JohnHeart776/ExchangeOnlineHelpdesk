<?php

class MailController
{

    /**
	 * Returns all objects from the table.
	 *
	 * @param int         $limit     Optional limit (default: 0 = no limit)
	 * @param string|null $direction Optional sort mode ("ASC" or "DESC"), null = no sorting
	 * @param string|null $sortBy    Optional column name for sorting, null = uses the first field
	 * @return Mail[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getAll(int $limit = 0, ?string $direction = null, ?string $sortBy = null): array
    {
        global $d;
        $_q = "SELECT `MailId` FROM `Mail`";

        // Sortierung nur anwenden, wenn $direction gesetzt ist
        if ($direction !== null) {
            $direction = strtoupper($direction);
            if ($direction !== "ASC" && $direction !== "DESC") {
                throw new Exception("Invalid order parameter: " . $direction);
            }

            // If no sort field specified, use the first field
            if ($sortBy === null) {
                $sortBy = "MailId";
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
            $r[] = new Mail((int)$u["MailId"]);
        }
        return $r;
    }

    /**
     * @param int $id
     * @return Mail|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getById(int $id): ?Mail
    {
        global $d;
        $_q = "SELECT `MailId` FROM `Mail` WHERE `MailId` = ".$d->filter($id)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Mail((int)$t["MailId"]);
    }

    /**
     * @param string $guid
     * @return Mail|null
     * @throws \Database\DatabaseQueryException
     */
    public static function getByGuid(string $guid): ?Mail
    {
        global $d;
        $_q = "SELECT `MailId` FROM `Mail` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        return new Mail($t["MailId"]);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @return Mail|null
     * @throws \Database\DatabaseQueryException
     */
    public static function searchOneBy(string $field, string $term): ?Mail
    {
        return self::searchBy($field, $term, true);
    }

    /**
     * @param string $field
     * @param mixed $term
     * @param bool $fetchOne
     * @param int $limit Optionales Limit (Standard: 0 = kein Limit)
     * @return Mail|null|Mail[]
     * @throws \Database\DatabaseQueryException
     */
    public static function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 0)
    {
        global $d;
        $allowed = ["MailId", "Guid", "SecureObjectHash", "SourceMailbox", "AzureId", "MessageId", "TicketId", "Subject", "SenderName", "SenderEmail", "FromName", "FromEmail", "ToRecipients", "CcRecipients", "BccRecipients", "Body", "BodyType", "ReceivedDatetime", "SentDatetime", "Importance", "ConversationId", "BodyPreview", "HasAttachments", "MailHeadersRaw", "HeadersJson", "AzureObject", "CreatedAt"];
        if (!in_array($field, $allowed)) {
			throw new Exception("Invalid search field: " . $field);
		}
        $_q = "SELECT `MailId` FROM `Mail` WHERE `$field` LIKE \"".$d->filter($term)."\"";

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
            return new Mail((int)$result["MailId"]);
        } else {
            $results = $d->get($_q);
            $arr = [];
            foreach ($results as $row) {
                $arr[] = new Mail((int)$row["MailId"]);
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
        $allowed = ["MailId", "Guid", "SecureObjectHash", "SourceMailbox", "AzureId", "MessageId", "TicketId", "Subject", "SenderName", "SenderEmail", "FromName", "FromEmail", "ToRecipients", "CcRecipients", "BccRecipients", "Body", "BodyType", "ReceivedDatetime", "SentDatetime", "Importance", "ConversationId", "BodyPreview", "HasAttachments", "MailHeadersRaw", "HeadersJson", "AzureObject", "CreatedAt"];
        if (!in_array($field, $allowed)) {
            throw new Exception("Ungültiges Suchfeld: " . $field);
        }
        $_q = "SELECT `MailId` FROM `Mail` WHERE `$field` LIKE \"%".$d->filter($term)."%\" LIMIT 1";
        $result = $d->get($_q, true);
        return !empty($result);
    }

    /**
	 * Returns random objects from the table.
	 *
	 * @param int $amount Number of records to return (default: 1)
	 * @return Mail[]
     * @throws \Database\DatabaseQueryException
     */
    public static function getRandom(int $amount = 1): array
    {
        global $d;
        // At least 1
        $amount = max(1, $amount);
        $_q = "SELECT `MailId` FROM `Mail` ORDER BY RAND() LIMIT " . $d->filter($amount) . ";";
        $results = $d->get($_q);
        $arr = [];
        foreach ($results as $row) {
            $arr[] = new Mail((int)$row["MailId"]);
        }
        return $arr;
    }

    /**
     * @param Mail $obj
     * @return Mail|null
     * @throws \Database\DatabaseQueryException
     */
    public static function save(Mail $obj): ?Mail {
        global $d;
        // Überprüfe, ob der Primary Key (das erste Feld) leer ist
        if (!empty($obj->MailId)) {
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
        if (isset($obj->SecureObjectHash)) {
            $cols[] = "`SecureObjectHash`";
            $vals[] = "\"".$d->filter($obj->SecureObjectHash)."\"";
        }
        if (isset($obj->SourceMailbox)) {
            $cols[] = "`SourceMailbox`";
            $vals[] = "\"".$d->filter($obj->SourceMailbox)."\"";
        }
        if (isset($obj->AzureId)) {
            $cols[] = "`AzureId`";
            $vals[] = "\"".$d->filter($obj->AzureId)."\"";
        }
        if (isset($obj->MessageId)) {
            $cols[] = "`MessageId`";
            $vals[] = "\"".$d->filter($obj->MessageId)."\"";
        }
        if (isset($obj->TicketId)) {
            $cols[] = "`TicketId`";
            $vals[] = "\"".$d->filter($obj->TicketId)."\"";
        }
        if (isset($obj->Subject)) {
            $cols[] = "`Subject`";
            $vals[] = "\"".$d->filter($obj->Subject)."\"";
        }
        if (isset($obj->SenderName)) {
            $cols[] = "`SenderName`";
            $vals[] = "\"".$d->filter($obj->SenderName)."\"";
        }
        if (isset($obj->SenderEmail)) {
            $cols[] = "`SenderEmail`";
            $vals[] = "\"".$d->filter($obj->SenderEmail)."\"";
        }
        if (isset($obj->FromName)) {
            $cols[] = "`FromName`";
            $vals[] = "\"".$d->filter($obj->FromName)."\"";
        }
        if (isset($obj->FromEmail)) {
            $cols[] = "`FromEmail`";
            $vals[] = "\"".$d->filter($obj->FromEmail)."\"";
        }
        if (isset($obj->ToRecipients)) {
            $cols[] = "`ToRecipients`";
            $vals[] = "\"".$d->filter($obj->ToRecipients)."\"";
        }
        if (isset($obj->CcRecipients)) {
            $cols[] = "`CcRecipients`";
            $vals[] = "\"".$d->filter($obj->CcRecipients)."\"";
        }
        if (isset($obj->BccRecipients)) {
            $cols[] = "`BccRecipients`";
            $vals[] = "\"".$d->filter($obj->BccRecipients)."\"";
        }
        if (isset($obj->Body)) {
            $cols[] = "`Body`";
            $vals[] = "\"".$d->filter($obj->Body)."\"";
        }
        if (isset($obj->BodyType)) {
            $cols[] = "`BodyType`";
            $vals[] = "\"".$d->filter($obj->BodyType)."\"";
        }
        if (isset($obj->ReceivedDatetime)) {
            $cols[] = "`ReceivedDatetime`";
            $vals[] = "\"".$d->filter($obj->ReceivedDatetime)."\"";
        }
        if (isset($obj->SentDatetime)) {
            $cols[] = "`SentDatetime`";
            $vals[] = "\"".$d->filter($obj->SentDatetime)."\"";
        }
        if (isset($obj->Importance)) {
            $cols[] = "`Importance`";
            $vals[] = "\"".$d->filter($obj->Importance)."\"";
        }
        if (isset($obj->ConversationId)) {
            $cols[] = "`ConversationId`";
            $vals[] = "\"".$d->filter($obj->ConversationId)."\"";
        }
        if (isset($obj->BodyPreview)) {
            $cols[] = "`BodyPreview`";
            $vals[] = "\"".$d->filter($obj->BodyPreview)."\"";
        }
        if (isset($obj->HasAttachments)) {
            $cols[] = "`HasAttachments`";
            $vals[] = "\"".$d->filter($obj->HasAttachments)."\"";
        }
        if (isset($obj->MailHeadersRaw)) {
            $cols[] = "`MailHeadersRaw`";
            $vals[] = "\"".$d->filter($obj->MailHeadersRaw)."\"";
        }
        if (isset($obj->HeadersJson)) {
            $cols[] = "`HeadersJson`";
            $vals[] = "\"".$d->filter($obj->HeadersJson)."\"";
        }
        if (isset($obj->AzureObject)) {
            $cols[] = "`AzureObject`";
            $vals[] = "\"".$d->filter($obj->AzureObject)."\"";
        }
        $cols[] = "`CreatedAt`";
        $vals[] = "NOW()";
        $_q = "INSERT INTO `Mail` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        if(!$d->query($_q)) {
            throw new Exception("Insert failed");
        }
        $id = $d->lastInsertIdFromMysqli();
        return new Mail((int)$id);
    }

}
