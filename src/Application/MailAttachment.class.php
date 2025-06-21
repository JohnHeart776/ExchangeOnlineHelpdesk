<?php

class MailAttachment
{

    //Trait
    use MailAttachmentTrait;

    public ?int $MailAttachmentId = null;
    public ?string $Guid = null;
    public ?string $AzureId = null;
    public ?string $Secret1 = null;
    public ?string $Secret2 = null;
    public ?string $Secret3 = null;
    public ?int $MailId = null;
    public ?string $Name = null;
    public ?string $ContentType = null;
    public ?int $Size = null;
    public ?int $IsInline = null;
    public ?string $HashSha256 = null;
    public ?string $Content = null;
    public ?string $TextRepresentation = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->MailAttachmentId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->MailAttachmentId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT MailAttachmentId FROM `MailAttachment` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['MailAttachmentId'];
    }

    public function isValid(): bool {
        return (bool)$this->MailAttachmentId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `MailAttachmentId`, `Guid`, `AzureId`, `Secret1`, `Secret2`, `Secret3`, `MailId`, `Name`, `ContentType`, `Size`, `IsInline`, `HashSha256`, `Content`, `TextRepresentation`, `CreatedAt` FROM `MailAttachment` WHERE `MailAttachmentId` = ".$d->filter($this->MailAttachmentId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->MailAttachmentId = (int)$t['MailAttachmentId'];
        $this->Guid = $t['Guid'];
        $this->AzureId = $t['AzureId'];
        $this->Secret1 = $t['Secret1'];
        $this->Secret2 = $t['Secret2'];
        $this->Secret3 = $t['Secret3'];
        $this->MailId = (int)$t['MailId'];
        $this->Name = $t['Name'];
        $this->ContentType = $t['ContentType'];
        $this->Size = (int)$t['Size'];
        $this->IsInline = (int)$t['IsInline'];
        $this->HashSha256 = $t['HashSha256'];
        $this->Content = $t['Content'];
        $this->TextRepresentation = $t['TextRepresentation'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`AzureId` = \"" . $d->filter($this->AzureId) . "\"";
        $updates[] = "`Secret1` = \"" . $d->filter($this->Secret1) . "\"";
        $updates[] = "`Secret2` = \"" . $d->filter($this->Secret2) . "\"";
        $updates[] = "`Secret3` = \"" . $d->filter($this->Secret3) . "\"";
        $updates[] = "`MailId` = " . $d->filter((int)$this->MailId);
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`ContentType` = \"" . $d->filter($this->ContentType) . "\"";
        $updates[] = "`Size` = " . $d->filter((int)$this->Size);
        $updates[] = "`IsInline` = " . $d->filter((int)$this->IsInline);
        $updates[] = "`HashSha256` = \"" . $d->filter($this->HashSha256) . "\"";
        $updates[] = "`Content` = \"" . $d->filter($this->Content) . "\"";
        $updates[] = "`TextRepresentation` = \"" . $d->filter($this->TextRepresentation) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `MailAttachment` SET " . implode(", ", $updates) . " WHERE `MailAttachmentId` = " . $d->filter($this->MailAttachmentId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'MailAttachmentId':
            case 'MailId':
            case 'Size':
            case 'IsInline':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'AzureId':
            case 'Secret1':
            case 'Secret2':
            case 'Secret3':
            case 'Name':
            case 'ContentType':
            case 'HashSha256':
            case 'Content':
            case 'TextRepresentation':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `MailAttachment` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `MailAttachment` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::MailAttachment();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for MailAttachment::$key");
        return $isUpdatable;
    }

    public function getMailAttachmentId(){
        return $this->MailAttachmentId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getAzureId(){
        return $this->AzureId;
    }

    public function getSecret1(){
        return $this->Secret1;
    }

    public function getSecret2(){
        return $this->Secret2;
    }

    public function getSecret3(){
        return $this->Secret3;
    }

    public function getMailId(){
        return $this->MailId;
    }

    public function getName(){
        return $this->Name;
    }

    public function getContentType(){
        return $this->ContentType;
    }

    public function getSize(){
        return $this->Size;
    }

    public function getIsInline(){
        return $this->IsInline;
    }

    public function getHashSha256(){
        return $this->HashSha256;
    }

    public function getContent(){
        return $this->Content;
    }

    public function getTextRepresentation(){
        return $this->TextRepresentation;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getMailAttachmentIdAsInt(): int {
        return (int)$this->MailAttachmentId;
    }

    public function getMailAttachmentIdAsBool(): bool {
        return (bool)$this->MailAttachmentId;
    }

    public function getMailIdAsInt(): int {
        return (int)$this->MailId;
    }

    public function getMailIdAsBool(): bool {
        return (bool)$this->MailId;
    }

    public function getSizeAsInt(): int {
        return (int)$this->Size;
    }

    public function getSizeAsBool(): bool {
        return (bool)$this->Size;
    }

    public function getIsInlineAsInt(): int {
        return (int)$this->IsInline;
    }

    public function getIsInlineAsBool(): bool {
        return (bool)$this->IsInline;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?MailAttachment $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
