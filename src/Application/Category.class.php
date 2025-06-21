<?php

class Category
{

    //Trait
    use CategoryTrait;

    public ?int $CategoryId = null;
    public ?string $Guid = null;
    public ?string $InternalName = null;
    public ?string $PublicName = null;
    public ?string $Icon = null;
    public ?string $Color = null;
    public ?int $IsDefault = null;
    public ?int $SortOrder = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->CategoryId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->CategoryId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT CategoryId FROM `Category` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['CategoryId'];
    }

    public function isValid(): bool {
        return (bool)$this->CategoryId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `CategoryId`, `Guid`, `InternalName`, `PublicName`, `Icon`, `Color`, `IsDefault`, `SortOrder`, `CreatedAt` FROM `Category` WHERE `CategoryId` = ".$d->filter($this->CategoryId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->CategoryId = (int)$t['CategoryId'];
        $this->Guid = $t['Guid'];
        $this->InternalName = $t['InternalName'];
        $this->PublicName = $t['PublicName'];
        $this->Icon = $t['Icon'];
        $this->Color = $t['Color'];
        $this->IsDefault = (int)$t['IsDefault'];
        $this->SortOrder = (int)$t['SortOrder'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`InternalName` = \"" . $d->filter($this->InternalName) . "\"";
        $updates[] = "`PublicName` = \"" . $d->filter($this->PublicName) . "\"";
        $updates[] = "`Icon` = \"" . $d->filter($this->Icon) . "\"";
        $updates[] = "`Color` = \"" . $d->filter($this->Color) . "\"";
        $updates[] = "`IsDefault` = " . $d->filter((int)$this->IsDefault);
        $updates[] = "`SortOrder` = " . $d->filter((int)$this->SortOrder);
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `Category` SET " . implode(", ", $updates) . " WHERE `CategoryId` = " . $d->filter($this->CategoryId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'CategoryId':
            case 'IsDefault':
            case 'SortOrder':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'InternalName':
            case 'PublicName':
            case 'Icon':
            case 'Color':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Category` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Category` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Category();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Category::$key");
        return $isUpdatable;
    }

    public function getCategoryId(){
        return $this->CategoryId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getInternalName(){
        return $this->InternalName;
    }

    public function getPublicName(){
        return $this->PublicName;
    }

    public function getIcon(){
        return $this->Icon;
    }

    public function getColor(){
        return $this->Color;
    }

    public function getIsDefault(){
        return $this->IsDefault;
    }

    public function getSortOrder(){
        return $this->SortOrder;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getCategoryIdAsInt(): int {
        return (int)$this->CategoryId;
    }

    public function getCategoryIdAsBool(): bool {
        return (bool)$this->CategoryId;
    }

    public function getIsDefaultAsInt(): int {
        return (int)$this->IsDefault;
    }

    public function getIsDefaultAsBool(): bool {
        return (bool)$this->IsDefault;
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

    public function equals(?Category $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
