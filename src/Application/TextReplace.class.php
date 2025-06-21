<?php

class TextReplace
{

    //Trait
    use TextReplaceTrait;

    public ?int $TextReplaceId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?string $SearchFor = null;
    public ?string $ReplaceBy = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TextReplaceId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TextReplaceId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TextReplaceId FROM `TextReplace` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TextReplaceId'];
    }

    public function isValid(): bool {
        return (bool)$this->TextReplaceId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TextReplaceId`, `Guid`, `Enabled`, `SearchFor`, `ReplaceBy` FROM `TextReplace` WHERE `TextReplaceId` = ".$d->filter($this->TextReplaceId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TextReplaceId = (int)$t['TextReplaceId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->SearchFor = $t['SearchFor'];
        $this->ReplaceBy = $t['ReplaceBy'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`SearchFor` = \"" . $d->filter($this->SearchFor) . "\"";
        $updates[] = "`ReplaceBy` = \"" . $d->filter($this->ReplaceBy) . "\"";
        $_q = "UPDATE `TextReplace` SET " . implode(", ", $updates) . " WHERE `TextReplaceId` = " . $d->filter($this->TextReplaceId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TextReplaceId':
            case 'Enabled':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'SearchFor':
            case 'ReplaceBy':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TextReplace` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TextReplace` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TextReplace();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TextReplace::$key");
        return $isUpdatable;
    }

    public function getTextReplaceId(){
        return $this->TextReplaceId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getSearchFor(){
        return $this->SearchFor;
    }

    public function getReplaceBy(){
        return $this->ReplaceBy;
    }

    public function getTextReplaceIdAsInt(): int {
        return (int)$this->TextReplaceId;
    }

    public function getTextReplaceIdAsBool(): bool {
        return (bool)$this->TextReplaceId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function equals(?TextReplace $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
