<?php

class File
{

    //Trait
    use FileTrait;

    public ?int $FileId = null;
    public ?string $Guid = null;
    public ?string $Secret1 = null;
    public ?string $Secret2 = null;
    public ?string $Secret3 = null;
    public ?string $HashSha256 = null;
    public ?string $CreatedDatetime = null;
    public ?string $Name = null;
    public ?int $Size = null;
    public ?string $Type = null;
    public ?string $Data = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->FileId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->FileId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT FileId FROM `File` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['FileId'];
    }

    public function isValid(): bool {
        return (bool)$this->FileId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `FileId`, `Guid`, `Secret1`, `Secret2`, `Secret3`, `HashSha256`, `CreatedDatetime`, `Name`, `Size`, `Type`, `Data` FROM `File` WHERE `FileId` = ".$d->filter($this->FileId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->FileId = (int)$t['FileId'];
        $this->Guid = $t['Guid'];
        $this->Secret1 = $t['Secret1'];
        $this->Secret2 = $t['Secret2'];
        $this->Secret3 = $t['Secret3'];
        $this->HashSha256 = $t['HashSha256'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->Name = $t['Name'];
        $this->Size = (int)$t['Size'];
        $this->Type = $t['Type'];
        $this->Data = $t['Data'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Secret1` = \"" . $d->filter($this->Secret1) . "\"";
        $updates[] = "`Secret2` = \"" . $d->filter($this->Secret2) . "\"";
        $updates[] = "`Secret3` = \"" . $d->filter($this->Secret3) . "\"";
        $updates[] = "`HashSha256` = \"" . $d->filter($this->HashSha256) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`Size` = " . $d->filter((int)$this->Size);
        $updates[] = "`Type` = \"" . $d->filter($this->Type) . "\"";
        $updates[] = "`Data` = \"" . $d->filter($this->Data) . "\"";
        $_q = "UPDATE `File` SET " . implode(", ", $updates) . " WHERE `FileId` = " . $d->filter($this->FileId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'FileId':
            case 'Size':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Secret1':
            case 'Secret2':
            case 'Secret3':
            case 'HashSha256':
            case 'CreatedDatetime':
            case 'Name':
            case 'Type':
            case 'Data':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `File` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `File` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::File();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for File::$key");
        return $isUpdatable;
    }

    public function getFileId(){
        return $this->FileId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getSecret1(){
        return $this->Secret1;
    }

    public function getSecret2(){
        return $this->Secret2;
    }

    public function getSecret3(){
        return $this->Secret3;
    }

    public function getHashSha256(){
        return $this->HashSha256;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getName(){
        return $this->Name;
    }

    public function getSize(){
        return $this->Size;
    }

    public function getType(){
        return $this->Type;
    }

    public function getData(){
        return $this->Data;
    }

    public function getFileIdAsInt(): int {
        return (int)$this->FileId;
    }

    public function getFileIdAsBool(): bool {
        return (bool)$this->FileId;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function getSizeAsInt(): int {
        return (int)$this->Size;
    }

    public function getSizeAsBool(): bool {
        return (bool)$this->Size;
    }

    public function equals(?File $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
