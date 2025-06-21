<?php

class NotificationTemplate
{

    //Trait
    use NotificationTemplateTrait;

    public ?int $NotificationTemplateId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?string $InternalName = null;
    public ?string $Name = null;
    public ?string $MailSubject = null;
    public ?string $MailText = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->NotificationTemplateId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->NotificationTemplateId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT NotificationTemplateId FROM `NotificationTemplate` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['NotificationTemplateId'];
    }

    public function isValid(): bool {
        return (bool)$this->NotificationTemplateId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `NotificationTemplateId`, `Guid`, `Enabled`, `InternalName`, `Name`, `MailSubject`, `MailText` FROM `NotificationTemplate` WHERE `NotificationTemplateId` = ".$d->filter($this->NotificationTemplateId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->NotificationTemplateId = (int)$t['NotificationTemplateId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->InternalName = $t['InternalName'];
        $this->Name = $t['Name'];
        $this->MailSubject = $t['MailSubject'];
        $this->MailText = $t['MailText'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`InternalName` = \"" . $d->filter($this->InternalName) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`MailSubject` = \"" . $d->filter($this->MailSubject) . "\"";
        $updates[] = "`MailText` = \"" . $d->filter($this->MailText) . "\"";
        $_q = "UPDATE `NotificationTemplate` SET " . implode(", ", $updates) . " WHERE `NotificationTemplateId` = " . $d->filter($this->NotificationTemplateId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'NotificationTemplateId':
            case 'Enabled':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'InternalName':
            case 'Name':
            case 'MailSubject':
            case 'MailText':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `NotificationTemplate` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `NotificationTemplate` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::NotificationTemplate();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for NotificationTemplate::$key");
        return $isUpdatable;
    }

    public function getNotificationTemplateId(){
        return $this->NotificationTemplateId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getInternalName(){
        return $this->InternalName;
    }

    public function getName(){
        return $this->Name;
    }

    public function getMailSubject(){
        return $this->MailSubject;
    }

    public function getMailText(){
        return $this->MailText;
    }

    public function getNotificationTemplateIdAsInt(): int {
        return (int)$this->NotificationTemplateId;
    }

    public function getNotificationTemplateIdAsBool(): bool {
        return (bool)$this->NotificationTemplateId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function equals(?NotificationTemplate $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
