<?php

class ActionGroup
{

    //Trait
    use ActionGroupTrait;

    public ?int $ActionGroupId = null;
    public ?string $Guid = null;
    public ?string $Name = null;
    public ?string $Description = null;
    public ?int $SortOrder = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->ActionGroupId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->ActionGroupId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT ActionGroupId FROM `ActionGroup` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['ActionGroupId'];
    }

    public function isValid(): bool {
        return (bool)$this->ActionGroupId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `ActionGroupId`, `Guid`, `Name`, `Description`, `SortOrder`, `CreatedAt` FROM `ActionGroup` WHERE `ActionGroupId` = ".$d->filter($this->ActionGroupId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->ActionGroupId = (int)$t['ActionGroupId'];
        $this->Guid = $t['Guid'];
        $this->Name = $t['Name'];
        $this->Description = $t['Description'];
        $this->SortOrder = (int)$t['SortOrder'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`Description` = \"" . $d->filter($this->Description) . "\"";
        $updates[] = "`SortOrder` = " . $d->filter((int)$this->SortOrder);
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `ActionGroup` SET " . implode(", ", $updates) . " WHERE `ActionGroupId` = " . $d->filter($this->ActionGroupId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'ActionGroupId':
            case 'SortOrder':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Name':
            case 'Description':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `ActionGroup` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `ActionGroup` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::ActionGroup();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for ActionGroup::$key");
        return $isUpdatable;
    }

    public function getActionGroupId(){
        return $this->ActionGroupId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getName(){
        return $this->Name;
    }

    public function getDescription(){
        return $this->Description;
    }

    public function getSortOrder(){
        return $this->SortOrder;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getActionGroupIdAsInt(): int {
        return (int)$this->ActionGroupId;
    }

    public function getActionGroupIdAsBool(): bool {
        return (bool)$this->ActionGroupId;
    }

    public function getSortOrderAsInt(): int {
        return (int)$this->SortOrder;
    }

    public function getSortOrderAsBool(): bool {
        return (bool)$this->SortOrder;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?ActionGroup $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
