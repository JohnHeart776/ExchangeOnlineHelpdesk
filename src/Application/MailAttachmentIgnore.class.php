<?php

class MailAttachmentIgnore
{

    //Trait
    use MailAttachmentIgnoreTrait;

    public ?int $MailAttachmentIgnoreId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?string $HashSha256 = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->MailAttachmentIgnoreId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->MailAttachmentIgnoreId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT MailAttachmentIgnoreId FROM `MailAttachmentIgnore` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['MailAttachmentIgnoreId'];
    }

    public function isValid(): bool {
        return (bool)$this->MailAttachmentIgnoreId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `MailAttachmentIgnoreId`, `Guid`, `Enabled`, `HashSha256`, `CreatedAt` FROM `MailAttachmentIgnore` WHERE `MailAttachmentIgnoreId` = ".$d->filter($this->MailAttachmentIgnoreId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->MailAttachmentIgnoreId = (int)$t['MailAttachmentIgnoreId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->HashSha256 = $t['HashSha256'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`HashSha256` = \"" . $d->filter($this->HashSha256) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `MailAttachmentIgnore` SET " . implode(", ", $updates) . " WHERE `MailAttachmentIgnoreId` = " . $d->filter($this->MailAttachmentIgnoreId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'MailAttachmentIgnoreId':
            case 'Enabled':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'HashSha256':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `MailAttachmentIgnore` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `MailAttachmentIgnore` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function toggleValue(string $key, ?array $toggleStates = null): bool {
        // reuse your allowed‐check
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }

        // grab current raw value
        $current = $this->$key;

        if ($toggleStates === null) {
            // relaxed boolean toggle: allow int or string
            if (!in_array($current, [0, 1, '0', '1'], true)) {
                throw new Exception("Cannot toggle non‐boolean value for {$key}");
            }
            // normalize and flip
            $current  = (int)$current;
            $newValue = $current === 1 ? 0 : 1;
        } else {
            // arbitrary two‐state toggle
            if (!in_array($current, $toggleStates, true)) {
                throw new Exception("Current value {$current} for {$key} not in toggleStates");
            }
            $newValue = $toggleStates[0] === $current ? $toggleStates[1] : $toggleStates[0];
        }

        // perform the update (will re‐check permissions & UpdatedAt)
        return $this->update($key, $newValue);
    }

    public function isUpdateAllowedKey($key): bool {
        $a = \Updatable::MailAttachmentIgnore();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for MailAttachmentIgnore::$key");
        return $isUpdatable;
    }

    public function getMailAttachmentIgnoreId(){
        return $this->MailAttachmentIgnoreId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getHashSha256(){
        return $this->HashSha256;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getMailAttachmentIgnoreIdAsInt(): int {
        return (int)$this->MailAttachmentIgnoreId;
    }

    public function getMailAttachmentIgnoreIdAsBool(): bool {
        return (bool)$this->MailAttachmentIgnoreId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?MailAttachmentIgnore $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
