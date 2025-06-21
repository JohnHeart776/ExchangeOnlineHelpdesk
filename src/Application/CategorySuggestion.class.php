<?php

class CategorySuggestion
{

    //Trait
    use CategorySuggestionTrait;

    public ?int $CategorySuggestionId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?int $Priority = null;
    public ?string $Filter = null;
    public ?int $CategoryId = null;
    public ?int $AutoClose = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->CategorySuggestionId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->CategorySuggestionId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT CategorySuggestionId FROM `CategorySuggestion` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['CategorySuggestionId'];
    }

    public function isValid(): bool {
        return (bool)$this->CategorySuggestionId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `CategorySuggestionId`, `Guid`, `Enabled`, `Priority`, `Filter`, `CategoryId`, `AutoClose` FROM `CategorySuggestion` WHERE `CategorySuggestionId` = ".$d->filter($this->CategorySuggestionId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->CategorySuggestionId = (int)$t['CategorySuggestionId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->Priority = (int)$t['Priority'];
        $this->Filter = $t['Filter'];
        $this->CategoryId = (int)$t['CategoryId'];
        $this->AutoClose = (int)$t['AutoClose'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`Priority` = " . $d->filter((int)$this->Priority);
        $updates[] = "`Filter` = \"" . $d->filter($this->Filter) . "\"";
        $updates[] = "`CategoryId` = " . $d->filter((int)$this->CategoryId);
        $updates[] = "`AutoClose` = " . $d->filter((int)$this->AutoClose);
        $_q = "UPDATE `CategorySuggestion` SET " . implode(", ", $updates) . " WHERE `CategorySuggestionId` = " . $d->filter($this->CategorySuggestionId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'CategorySuggestionId':
            case 'Enabled':
            case 'Priority':
            case 'CategoryId':
            case 'AutoClose':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Filter':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `CategorySuggestion` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `CategorySuggestion` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::CategorySuggestion();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for CategorySuggestion::$key");
        return $isUpdatable;
    }

    public function getCategorySuggestionId(){
        return $this->CategorySuggestionId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getPriority(){
        return $this->Priority;
    }

    public function getFilter(){
        return $this->Filter;
    }

    public function getCategoryId(){
        return $this->CategoryId;
    }

    public function getAutoClose(){
        return $this->AutoClose;
    }

    public function getCategorySuggestionIdAsInt(): int {
        return (int)$this->CategorySuggestionId;
    }

    public function getCategorySuggestionIdAsBool(): bool {
        return (bool)$this->CategorySuggestionId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function getPriorityAsInt(): int {
        return (int)$this->Priority;
    }

    public function getPriorityAsBool(): bool {
        return (bool)$this->Priority;
    }

    public function getCategoryIdAsInt(): int {
        return (int)$this->CategoryId;
    }

    public function getCategoryIdAsBool(): bool {
        return (bool)$this->CategoryId;
    }

    public function getAutoCloseAsInt(): int {
        return (int)$this->AutoClose;
    }

    public function getAutoCloseAsBool(): bool {
        return (bool)$this->AutoClose;
    }

    public function equals(?CategorySuggestion $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
