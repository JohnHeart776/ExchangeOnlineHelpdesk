<?php

class TemplateText
{

    //Trait
    use TemplateTextTrait;

    public ?int $TemplateTextId = null;
    public ?string $Guid = null;
    public ?string $Name = null;
    public ?string $Description = null;
    public ?string $Content = null;
    public ?string $CreatedDatetime = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TemplateTextId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TemplateTextId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TemplateTextId FROM `TemplateText` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TemplateTextId'];
    }

    public function isValid(): bool {
        return (bool)$this->TemplateTextId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TemplateTextId`, `Guid`, `Name`, `Description`, `Content`, `CreatedDatetime` FROM `TemplateText` WHERE `TemplateTextId` = ".$d->filter($this->TemplateTextId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TemplateTextId = (int)$t['TemplateTextId'];
        $this->Guid = $t['Guid'];
        $this->Name = $t['Name'];
        $this->Description = $t['Description'];
        $this->Content = $t['Content'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`Description` = \"" . $d->filter($this->Description) . "\"";
        $updates[] = "`Content` = \"" . $d->filter($this->Content) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $_q = "UPDATE `TemplateText` SET " . implode(", ", $updates) . " WHERE `TemplateTextId` = " . $d->filter($this->TemplateTextId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TemplateTextId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Name':
            case 'Description':
            case 'Content':
            case 'CreatedDatetime':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TemplateText` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TemplateText` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TemplateText();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TemplateText::$key");
        return $isUpdatable;
    }

    public function getTemplateTextId(){
        return $this->TemplateTextId;
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

    public function getContent(){
        return $this->Content;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getTemplateTextIdAsInt(): int {
        return (int)$this->TemplateTextId;
    }

    public function getTemplateTextIdAsBool(): bool {
        return (bool)$this->TemplateTextId;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function equals(?TemplateText $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
