<?php

class MenuItem
{

    //Trait
    use MenuItemTrait;

    public ?int $MenuItemId = null;
    public ?string $Guid = null;
    public ?int $MenuId = null;
    public ?int $ParentMenuItemId = null;
    public ?int $SortOrder = null;
    public ?int $Enabled = null;
    public ?string $Title = null;
    public ?string $Link = null;
    public ?string $Icon = null;
    public ?string $Color = null;
    public ?int $ImageFileId = null;
    public ?int $requireIsUser = null;
    public ?int $requireIsAgent = null;
    public ?int $requireIsAdmin = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->MenuItemId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->MenuItemId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT MenuItemId FROM `MenuItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['MenuItemId'];
    }

    public function isValid(): bool {
        return (bool)$this->MenuItemId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `MenuItemId`, `Guid`, `MenuId`, `ParentMenuItemId`, `SortOrder`, `Enabled`, `Title`, `Link`, `Icon`, `Color`, `ImageFileId`, `requireIsUser`, `requireIsAgent`, `requireIsAdmin` FROM `MenuItem` WHERE `MenuItemId` = ".$d->filter($this->MenuItemId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->MenuItemId = (int)$t['MenuItemId'];
        $this->Guid = $t['Guid'];
        $this->MenuId = (int)$t['MenuId'];
        $this->ParentMenuItemId = (int)$t['ParentMenuItemId'];
        $this->SortOrder = (int)$t['SortOrder'];
        $this->Enabled = (int)$t['Enabled'];
        $this->Title = $t['Title'];
        $this->Link = $t['Link'];
        $this->Icon = $t['Icon'];
        $this->Color = $t['Color'];
        $this->ImageFileId = (int)$t['ImageFileId'];
        $this->requireIsUser = (int)$t['requireIsUser'];
        $this->requireIsAgent = (int)$t['requireIsAgent'];
        $this->requireIsAdmin = (int)$t['requireIsAdmin'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`MenuId` = " . $d->filter((int)$this->MenuId);
        $updates[] = "`ParentMenuItemId` = " . $d->filter((int)$this->ParentMenuItemId);
        $updates[] = "`SortOrder` = " . $d->filter((int)$this->SortOrder);
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`Title` = \"" . $d->filter($this->Title) . "\"";
        $updates[] = "`Link` = \"" . $d->filter($this->Link) . "\"";
        $updates[] = "`Icon` = \"" . $d->filter($this->Icon) . "\"";
        $updates[] = "`Color` = \"" . $d->filter($this->Color) . "\"";
        $updates[] = "`ImageFileId` = " . $d->filter((int)$this->ImageFileId);
        $updates[] = "`requireIsUser` = " . $d->filter((int)$this->requireIsUser);
        $updates[] = "`requireIsAgent` = " . $d->filter((int)$this->requireIsAgent);
        $updates[] = "`requireIsAdmin` = " . $d->filter((int)$this->requireIsAdmin);
        $_q = "UPDATE `MenuItem` SET " . implode(", ", $updates) . " WHERE `MenuItemId` = " . $d->filter($this->MenuItemId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'MenuItemId':
            case 'MenuId':
            case 'ParentMenuItemId':
            case 'SortOrder':
            case 'Enabled':
            case 'ImageFileId':
            case 'requireIsUser':
            case 'requireIsAgent':
            case 'requireIsAdmin':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Title':
            case 'Link':
            case 'Icon':
            case 'Color':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `MenuItem` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `MenuItem` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::MenuItem();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for MenuItem::$key");
        return $isUpdatable;
    }

    public function getMenuItemId(){
        return $this->MenuItemId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getMenuId(){
        return $this->MenuId;
    }

    public function getParentMenuItemId(){
        return $this->ParentMenuItemId;
    }

    public function getSortOrder(){
        return $this->SortOrder;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getLink(){
        return $this->Link;
    }

    public function getIcon(){
        return $this->Icon;
    }

    public function getColor(){
        return $this->Color;
    }

    public function getImageFileId(){
        return $this->ImageFileId;
    }

    public function getRequireIsUser(){
        return $this->requireIsUser;
    }

    public function getRequireIsAgent(){
        return $this->requireIsAgent;
    }

    public function getRequireIsAdmin(){
        return $this->requireIsAdmin;
    }

    public function getMenuItemIdAsInt(): int {
        return (int)$this->MenuItemId;
    }

    public function getMenuItemIdAsBool(): bool {
        return (bool)$this->MenuItemId;
    }

    public function getMenuIdAsInt(): int {
        return (int)$this->MenuId;
    }

    public function getMenuIdAsBool(): bool {
        return (bool)$this->MenuId;
    }

    public function getParentMenuItemIdAsInt(): int {
        return (int)$this->ParentMenuItemId;
    }

    public function getParentMenuItemIdAsBool(): bool {
        return (bool)$this->ParentMenuItemId;
    }

    public function getSortOrderAsInt(): int {
        return (int)$this->SortOrder;
    }

    public function getSortOrderAsBool(): bool {
        return (bool)$this->SortOrder;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function getImageFileIdAsInt(): int {
        return (int)$this->ImageFileId;
    }

    public function getImageFileIdAsBool(): bool {
        return (bool)$this->ImageFileId;
    }

    public function getRequireIsUserAsInt(): int {
        return (int)$this->requireIsUser;
    }

    public function getRequireIsUserAsBool(): bool {
        return (bool)$this->requireIsUser;
    }

    public function getRequireIsAgentAsInt(): int {
        return (int)$this->requireIsAgent;
    }

    public function getRequireIsAgentAsBool(): bool {
        return (bool)$this->requireIsAgent;
    }

    public function getRequireIsAdminAsInt(): int {
        return (int)$this->requireIsAdmin;
    }

    public function getRequireIsAdminAsBool(): bool {
        return (bool)$this->requireIsAdmin;
    }

    public function equals(?MenuItem $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
