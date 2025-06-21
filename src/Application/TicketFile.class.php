<?php

class TicketFile
{

    //Trait
    use TicketFileTrait;

    public ?int $TicketFileId = null;
    public ?string $Guid = null;
    public ?int $TicketId = null;
    public ?int $FileId = null;
    public ?int $UserId = null;
    public ?string $CreatedDatetime = null;
    public ?string $AccessLevel = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketFileId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketFileId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketFileId FROM `TicketFile` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketFileId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketFileId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketFileId`, `Guid`, `TicketId`, `FileId`, `UserId`, `CreatedDatetime`, `AccessLevel` FROM `TicketFile` WHERE `TicketFileId` = ".$d->filter($this->TicketFileId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketFileId = (int)$t['TicketFileId'];
        $this->Guid = $t['Guid'];
        $this->TicketId = (int)$t['TicketId'];
        $this->FileId = (int)$t['FileId'];
        $this->UserId = (int)$t['UserId'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->AccessLevel = $t['AccessLevel'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`FileId` = " . $d->filter((int)$this->FileId);
        $updates[] = "`UserId` = " . $d->filter((int)$this->UserId);
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`AccessLevel` = \"" . $d->filter($this->AccessLevel) . "\"";
        $_q = "UPDATE `TicketFile` SET " . implode(", ", $updates) . " WHERE `TicketFileId` = " . $d->filter($this->TicketFileId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketFileId':
            case 'TicketId':
            case 'FileId':
            case 'UserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'CreatedDatetime':
            case 'AccessLevel':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TicketFile` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TicketFile` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TicketFile();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TicketFile::$key");
        return $isUpdatable;
    }

    public function getTicketFileId(){
        return $this->TicketFileId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getFileId(){
        return $this->FileId;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getAccessLevel(){
        return $this->AccessLevel;
    }

    public function getTicketFileIdAsInt(): int {
        return (int)$this->TicketFileId;
    }

    public function getTicketFileIdAsBool(): bool {
        return (bool)$this->TicketFileId;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getFileIdAsInt(): int {
        return (int)$this->FileId;
    }

    public function getFileIdAsBool(): bool {
        return (bool)$this->FileId;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function equals(?TicketFile $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
