<?php

class ActionItem
{

    //Trait
    use ActionItemTrait;

    public ?int $ActionItemId = null;
    public ?string $Guid = null;
    public ?int $ActionGroupId = null;
    public ?string $Title = null;
    public ?string $Description = null;
    public ?int $IsOptional = null;
    public ?int $SortOrder = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->ActionItemId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->ActionItemId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT ActionItemId FROM `ActionItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['ActionItemId'];
    }

    public function isValid(): bool {
        return (bool)$this->ActionItemId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `ActionItemId`, `Guid`, `ActionGroupId`, `Title`, `Description`, `IsOptional`, `SortOrder` FROM `ActionItem` WHERE `ActionItemId` = ".$d->filter($this->ActionItemId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->ActionItemId = (int)$t['ActionItemId'];
        $this->Guid = $t['Guid'];
        $this->ActionGroupId = (int)$t['ActionGroupId'];
        $this->Title = $t['Title'];
        $this->Description = $t['Description'];
        $this->IsOptional = (int)$t['IsOptional'];
        $this->SortOrder = (int)$t['SortOrder'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`ActionGroupId` = " . $d->filter((int)$this->ActionGroupId);
        $updates[] = "`Title` = \"" . $d->filter($this->Title) . "\"";
        $updates[] = "`Description` = \"" . $d->filter($this->Description) . "\"";
        $updates[] = "`IsOptional` = " . $d->filter((int)$this->IsOptional);
        $updates[] = "`SortOrder` = " . $d->filter((int)$this->SortOrder);
        $_q = "UPDATE `ActionItem` SET " . implode(", ", $updates) . " WHERE `ActionItemId` = " . $d->filter($this->ActionItemId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'ActionItemId':
            case 'ActionGroupId':
            case 'IsOptional':
            case 'SortOrder':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Title':
            case 'Description':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `ActionItem` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `ActionItem` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::ActionItem();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for ActionItem::$key");
        return $isUpdatable;
    }

    public function getActionItemId(){
        return $this->ActionItemId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getActionGroupId(){
        return $this->ActionGroupId;
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getDescription(){
        return $this->Description;
    }

    public function getIsOptional(){
        return $this->IsOptional;
    }

    public function getSortOrder(){
        return $this->SortOrder;
    }

    public function getActionItemIdAsInt(): int {
        return (int)$this->ActionItemId;
    }

    public function getActionItemIdAsBool(): bool {
        return (bool)$this->ActionItemId;
    }

    public function getActionGroupIdAsInt(): int {
        return (int)$this->ActionGroupId;
    }

    public function getActionGroupIdAsBool(): bool {
        return (bool)$this->ActionGroupId;
    }

    public function getIsOptionalAsInt(): int {
        return (int)$this->IsOptional;
    }

    public function getIsOptionalAsBool(): bool {
        return (bool)$this->IsOptional;
    }

    public function getSortOrderAsInt(): int {
        return (int)$this->SortOrder;
    }

    public function getSortOrderAsBool(): bool {
        return (bool)$this->SortOrder;
    }

    public function equals(?ActionItem $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
