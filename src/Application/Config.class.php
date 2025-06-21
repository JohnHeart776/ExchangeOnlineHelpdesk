<?php

class Config
{

    //Trait
    use ConfigTrait;

    public ?int $ConfigId = null;
    public ?string $Guid = null;
    public ?string $Name = null;
    public ?string $Value = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->ConfigId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->ConfigId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT ConfigId FROM `Config` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['ConfigId'];
    }

    public function isValid(): bool {
        return (bool)$this->ConfigId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `ConfigId`, `Guid`, `Name`, `Value` FROM `Config` WHERE `ConfigId` = ".$d->filter($this->ConfigId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->ConfigId = (int)$t['ConfigId'];
        $this->Guid = $t['Guid'];
        $this->Name = $t['Name'];
        $this->Value = $t['Value'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`Value` = \"" . $d->filter($this->Value) . "\"";
        $_q = "UPDATE `Config` SET " . implode(", ", $updates) . " WHERE `ConfigId` = " . $d->filter($this->ConfigId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'ConfigId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Name':
            case 'Value':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Config` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Config` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Config();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Config::$key");
        return $isUpdatable;
    }

    public function getConfigId(){
        return $this->ConfigId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getName(){
        return $this->Name;
    }

    public function getValue(){
        return $this->Value;
    }

    public function getConfigIdAsInt(): int {
        return (int)$this->ConfigId;
    }

    public function getConfigIdAsBool(): bool {
        return (bool)$this->ConfigId;
    }

    public function equals(?Config $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
