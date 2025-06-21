<?php

class UserImage
{

    //Trait
    use UserImageTrait;

    public ?int $UserImageId = null;
    public ?string $Guid = null;
    public ?int $UserId = null;
    public ?string $Base64Image = null;
    public ?string $LastUpdated = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->UserImageId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->UserImageId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT UserImageId FROM `UserImage` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['UserImageId'];
    }

    public function isValid(): bool {
        return (bool)$this->UserImageId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `UserImageId`, `Guid`, `UserId`, `Base64Image`, `LastUpdated` FROM `UserImage` WHERE `UserImageId` = ".$d->filter($this->UserImageId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->UserImageId = (int)$t['UserImageId'];
        $this->Guid = $t['Guid'];
        $this->UserId = (int)$t['UserId'];
        $this->Base64Image = $t['Base64Image'];
        $this->LastUpdated = $t['LastUpdated'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`UserId` = " . $d->filter((int)$this->UserId);
        $updates[] = "`Base64Image` = \"" . $d->filter($this->Base64Image) . "\"";
        $updates[] = "`LastUpdated` = \"" . $d->filter(($this->LastUpdated instanceof DateTime ? $this->LastUpdated->format("Y-m-d H:i:s") : $this->LastUpdated)) . "\"";
        $_q = "UPDATE `UserImage` SET " . implode(", ", $updates) . " WHERE `UserImageId` = " . $d->filter($this->UserImageId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'UserImageId':
            case 'UserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Base64Image':
            case 'LastUpdated':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `UserImage` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `UserImage` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::UserImage();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for UserImage::$key");
        return $isUpdatable;
    }

    public function getUserImageId(){
        return $this->UserImageId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getBase64Image(){
        return $this->Base64Image;
    }

    public function getLastUpdated(){
        return $this->LastUpdated;
    }

    public function getUserImageIdAsInt(): int {
        return (int)$this->UserImageId;
    }

    public function getUserImageIdAsBool(): bool {
        return (bool)$this->UserImageId;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function getLastUpdatedAsDateTime(): DateTime {
        return ($this->LastUpdated instanceof DateTime) ? $this->LastUpdated : new DateTime($this->LastUpdated);
    }

    public function equals(?UserImage $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
