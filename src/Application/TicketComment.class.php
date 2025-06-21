<?php

class TicketComment
{

    //Trait
    use TicketCommentTrait;

    public ?int $TicketCommentId = null;
    public ?string $Guid = null;
    public ?int $TicketId = null;
    public ?int $UserId = null;
    public ?string $AccessLevel = null;
    public ?string $CreatedDatetime = null;
    public ?string $LastUpdatedDatetime = null;
    public ?string $Facility = null;
    public ?string $TextType = null;
    public ?string $Text = null;
    public ?int $MailId = null;
    public ?string $GraphObject = null;
    public ?int $IsEditable = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketCommentId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketCommentId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketCommentId FROM `TicketComment` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketCommentId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketCommentId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketCommentId`, `Guid`, `TicketId`, `UserId`, `AccessLevel`, `CreatedDatetime`, `LastUpdatedDatetime`, `Facility`, `TextType`, `Text`, `MailId`, `GraphObject`, `IsEditable` FROM `TicketComment` WHERE `TicketCommentId` = ".$d->filter($this->TicketCommentId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketCommentId = (int)$t['TicketCommentId'];
        $this->Guid = $t['Guid'];
        $this->TicketId = (int)$t['TicketId'];
        $this->UserId = (int)$t['UserId'];
        $this->AccessLevel = $t['AccessLevel'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->LastUpdatedDatetime = $t['LastUpdatedDatetime'];
        $this->Facility = $t['Facility'];
        $this->TextType = $t['TextType'];
        $this->Text = $t['Text'];
        $this->MailId = (int)$t['MailId'];
        $this->GraphObject = $t['GraphObject'];
        $this->IsEditable = (int)$t['IsEditable'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`UserId` = " . $d->filter((int)$this->UserId);
        $updates[] = "`AccessLevel` = \"" . $d->filter($this->AccessLevel) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`LastUpdatedDatetime` = \"" . $d->filter(($this->LastUpdatedDatetime instanceof DateTime ? $this->LastUpdatedDatetime->format("Y-m-d H:i:s") : $this->LastUpdatedDatetime)) . "\"";
        $updates[] = "`Facility` = \"" . $d->filter($this->Facility) . "\"";
        $updates[] = "`TextType` = \"" . $d->filter($this->TextType) . "\"";
        $updates[] = "`Text` = \"" . $d->filter($this->Text) . "\"";
        $updates[] = "`MailId` = " . $d->filter((int)$this->MailId);
        $updates[] = "`GraphObject` = \"" . $d->filter($this->GraphObject) . "\"";
        $updates[] = "`IsEditable` = " . $d->filter((int)$this->IsEditable);
        $_q = "UPDATE `TicketComment` SET " . implode(", ", $updates) . " WHERE `TicketCommentId` = " . $d->filter($this->TicketCommentId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketCommentId':
            case 'TicketId':
            case 'UserId':
            case 'MailId':
            case 'IsEditable':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'AccessLevel':
            case 'CreatedDatetime':
            case 'LastUpdatedDatetime':
            case 'Facility':
            case 'TextType':
            case 'Text':
            case 'GraphObject':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TicketComment` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TicketComment` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TicketComment();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TicketComment::$key");
        return $isUpdatable;
    }

    public function getTicketCommentId(){
        return $this->TicketCommentId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getAccessLevel(){
        return $this->AccessLevel;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getLastUpdatedDatetime(){
        return $this->LastUpdatedDatetime;
    }

    public function getFacility(){
        return $this->Facility;
    }

    public function getTextType(){
        return $this->TextType;
    }

    public function getText(){
        return $this->Text;
    }

    public function getMailId(){
        return $this->MailId;
    }

    public function getGraphObject(){
        return $this->GraphObject;
    }

    public function getIsEditable(){
        return $this->IsEditable;
    }

    public function getTicketCommentIdAsInt(): int {
        return (int)$this->TicketCommentId;
    }

    public function getTicketCommentIdAsBool(): bool {
        return (bool)$this->TicketCommentId;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function getLastUpdatedDatetimeAsDateTime(): DateTime {
        return ($this->LastUpdatedDatetime instanceof DateTime) ? $this->LastUpdatedDatetime : new DateTime($this->LastUpdatedDatetime);
    }

    public function getMailIdAsInt(): int {
        return (int)$this->MailId;
    }

    public function getMailIdAsBool(): bool {
        return (bool)$this->MailId;
    }

    public function getIsEditableAsInt(): int {
        return (int)$this->IsEditable;
    }

    public function getIsEditableAsBool(): bool {
        return (bool)$this->IsEditable;
    }

    public function equals(?TicketComment $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
