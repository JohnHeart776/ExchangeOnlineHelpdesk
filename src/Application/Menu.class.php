<?php

class Menu
{

    //Trait
    use MenuTrait;

    public ?int $MenuId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?string $Name = null;
    public ?int $SortNum = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->MenuId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->MenuId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT MenuId FROM `Menu` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['MenuId'];
    }

    public function isValid(): bool {
        return (bool)$this->MenuId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `MenuId`, `Guid`, `Enabled`, `Name`, `SortNum` FROM `Menu` WHERE `MenuId` = ".$d->filter($this->MenuId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->MenuId = (int)$t['MenuId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->Name = $t['Name'];
        $this->SortNum = (int)$t['SortNum'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`SortNum` = " . $d->filter((int)$this->SortNum);
        $_q = "UPDATE `Menu` SET " . implode(", ", $updates) . " WHERE `MenuId` = " . $d->filter($this->MenuId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'MenuId':
            case 'Enabled':
            case 'SortNum':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Name':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Menu` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Menu` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Menu();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Menu::$key");
        return $isUpdatable;
    }

    public function getMenuId(){
        return $this->MenuId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getName(){
        return $this->Name;
    }

    public function getSortNum(){
        return $this->SortNum;
    }

    public function getMenuIdAsInt(): int {
        return (int)$this->MenuId;
    }

    public function getMenuIdAsBool(): bool {
        return (bool)$this->MenuId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function getSortNumAsInt(): int {
        return (int)$this->SortNum;
    }

    public function getSortNumAsBool(): bool {
        return (bool)$this->SortNum;
    }

    public function equals(?Menu $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
