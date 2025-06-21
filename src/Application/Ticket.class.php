<?php

class Ticket
{

    //Trait
    use TicketTrait;

    public ?int $TicketId = null;
    public ?string $Guid = null;
    public ?string $Secret1 = null;
    public ?string $Secret2 = null;
    public ?string $Secret3 = null;
    public ?string $TicketNumber = null;
    public ?string $ConversationId = null;
    public ?int $StatusId = null;
    public ?string $MessengerName = null;
    public ?string $MessengerEmail = null;
    public ?string $Subject = null;
    public ?int $CategoryId = null;
    public ?int $AssigneeUserId = null;
    public ?string $DueDatetime = null;
    public ?string $CreatedDatetime = null;
    public ?string $UpdatedDatetime = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketId FROM `Ticket` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketId`, `Guid`, `Secret1`, `Secret2`, `Secret3`, `TicketNumber`, `ConversationId`, `StatusId`, `MessengerName`, `MessengerEmail`, `Subject`, `CategoryId`, `AssigneeUserId`, `DueDatetime`, `CreatedDatetime`, `UpdatedDatetime` FROM `Ticket` WHERE `TicketId` = ".$d->filter($this->TicketId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketId = (int)$t['TicketId'];
        $this->Guid = $t['Guid'];
        $this->Secret1 = $t['Secret1'];
        $this->Secret2 = $t['Secret2'];
        $this->Secret3 = $t['Secret3'];
        $this->TicketNumber = $t['TicketNumber'];
        $this->ConversationId = $t['ConversationId'];
        $this->StatusId = (int)$t['StatusId'];
        $this->MessengerName = $t['MessengerName'];
        $this->MessengerEmail = $t['MessengerEmail'];
        $this->Subject = $t['Subject'];
        $this->CategoryId = (int)$t['CategoryId'];
        $this->AssigneeUserId = (int)$t['AssigneeUserId'];
        $this->DueDatetime = $t['DueDatetime'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->UpdatedDatetime = $t['UpdatedDatetime'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Secret1` = \"" . $d->filter($this->Secret1) . "\"";
        $updates[] = "`Secret2` = \"" . $d->filter($this->Secret2) . "\"";
        $updates[] = "`Secret3` = \"" . $d->filter($this->Secret3) . "\"";
        $updates[] = "`TicketNumber` = \"" . $d->filter($this->TicketNumber) . "\"";
        $updates[] = "`ConversationId` = \"" . $d->filter($this->ConversationId) . "\"";
        $updates[] = "`StatusId` = " . $d->filter((int)$this->StatusId);
        $updates[] = "`MessengerName` = \"" . $d->filter($this->MessengerName) . "\"";
        $updates[] = "`MessengerEmail` = \"" . $d->filter($this->MessengerEmail) . "\"";
        $updates[] = "`Subject` = \"" . $d->filter($this->Subject) . "\"";
        $updates[] = "`CategoryId` = " . $d->filter((int)$this->CategoryId);
        $updates[] = "`AssigneeUserId` = " . $d->filter((int)$this->AssigneeUserId);
        $updates[] = "`DueDatetime` = \"" . $d->filter(($this->DueDatetime instanceof DateTime ? $this->DueDatetime->format("Y-m-d H:i:s") : $this->DueDatetime)) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`UpdatedDatetime` = \"" . $d->filter(($this->UpdatedDatetime instanceof DateTime ? $this->UpdatedDatetime->format("Y-m-d H:i:s") : $this->UpdatedDatetime)) . "\"";
        $_q = "UPDATE `Ticket` SET " . implode(", ", $updates) . " WHERE `TicketId` = " . $d->filter($this->TicketId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketId':
            case 'StatusId':
            case 'CategoryId':
            case 'AssigneeUserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Secret1':
            case 'Secret2':
            case 'Secret3':
            case 'TicketNumber':
            case 'ConversationId':
            case 'MessengerName':
            case 'MessengerEmail':
            case 'Subject':
            case 'DueDatetime':
            case 'CreatedDatetime':
            case 'UpdatedDatetime':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Ticket` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Ticket` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Ticket();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Ticket::$key");
        return $isUpdatable;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getGuid(){
        return $this->Guid;
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

    public function getTicketNumber(){
        return $this->TicketNumber;
    }

    public function getConversationId(){
        return $this->ConversationId;
    }

    public function getStatusId(){
        return $this->StatusId;
    }

    public function getMessengerName(){
        return $this->MessengerName;
    }

    public function getMessengerEmail(){
        return $this->MessengerEmail;
    }

    public function getSubject(){
        return $this->Subject;
    }

    public function getCategoryId(){
        return $this->CategoryId;
    }

    public function getAssigneeUserId(){
        return $this->AssigneeUserId;
    }

    public function getDueDatetime(){
        return $this->DueDatetime;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getUpdatedDatetime(){
        return $this->UpdatedDatetime;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getStatusIdAsInt(): int {
        return (int)$this->StatusId;
    }

    public function getStatusIdAsBool(): bool {
        return (bool)$this->StatusId;
    }

    public function getCategoryIdAsInt(): int {
        return (int)$this->CategoryId;
    }

    public function getCategoryIdAsBool(): bool {
        return (bool)$this->CategoryId;
    }

    public function getAssigneeUserIdAsInt(): int {
        return (int)$this->AssigneeUserId;
    }

    public function getAssigneeUserIdAsBool(): bool {
        return (bool)$this->AssigneeUserId;
    }

    public function getDueDatetimeAsDateTime(): DateTime {
        return ($this->DueDatetime instanceof DateTime) ? $this->DueDatetime : new DateTime($this->DueDatetime);
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function getUpdatedDatetimeAsDateTime(): DateTime {
        return ($this->UpdatedDatetime instanceof DateTime) ? $this->UpdatedDatetime : new DateTime($this->UpdatedDatetime);
    }

    public function equals(?Ticket $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
