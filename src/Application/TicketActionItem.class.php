<?php

class TicketActionItem
{

    //Trait
    use TicketActionItemTrait;

    public ?int $TicketActionItemId = null;
    public ?string $Guid = null;
    public ?int $TicketId = null;
    public ?int $ActionItemId = null;
    public ?string $Title = null;
    public ?string $Description = null;
    public ?string $DueDatetime = null;
    public ?string $Comment = null;
    public ?int $Completed = null;
    public ?int $CompletedByUserId = null;
    public ?string $CompletedAt = null;
    public ?string $CreatedAt = null;
    public ?int $CreatedByUserId = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketActionItemId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketActionItemId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketActionItemId FROM `TicketActionItem` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketActionItemId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketActionItemId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketActionItemId`, `Guid`, `TicketId`, `ActionItemId`, `Title`, `Description`, `DueDatetime`, `Comment`, `Completed`, `CompletedByUserId`, `CompletedAt`, `CreatedAt`, `CreatedByUserId` FROM `TicketActionItem` WHERE `TicketActionItemId` = ".$d->filter($this->TicketActionItemId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketActionItemId = (int)$t['TicketActionItemId'];
        $this->Guid = $t['Guid'];
        $this->TicketId = (int)$t['TicketId'];
        $this->ActionItemId = (int)$t['ActionItemId'];
        $this->Title = $t['Title'];
        $this->Description = $t['Description'];
        $this->DueDatetime = $t['DueDatetime'];
        $this->Comment = $t['Comment'];
        $this->Completed = (int)$t['Completed'];
        $this->CompletedByUserId = (int)$t['CompletedByUserId'];
        $this->CompletedAt = $t['CompletedAt'];
        $this->CreatedAt = $t['CreatedAt'];
        $this->CreatedByUserId = (int)$t['CreatedByUserId'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`ActionItemId` = " . $d->filter((int)$this->ActionItemId);
        $updates[] = "`Title` = \"" . $d->filter($this->Title) . "\"";
        $updates[] = "`Description` = \"" . $d->filter($this->Description) . "\"";
        $updates[] = "`DueDatetime` = \"" . $d->filter(($this->DueDatetime instanceof DateTime ? $this->DueDatetime->format("Y-m-d H:i:s") : $this->DueDatetime)) . "\"";
        $updates[] = "`Comment` = \"" . $d->filter($this->Comment) . "\"";
        $updates[] = "`Completed` = " . $d->filter((int)$this->Completed);
        $updates[] = "`CompletedByUserId` = " . $d->filter((int)$this->CompletedByUserId);
        $updates[] = "`CompletedAt` = \"" . $d->filter(($this->CompletedAt instanceof DateTime ? $this->CompletedAt->format("Y-m-d H:i:s") : $this->CompletedAt)) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $updates[] = "`CreatedByUserId` = " . $d->filter((int)$this->CreatedByUserId);
        $_q = "UPDATE `TicketActionItem` SET " . implode(", ", $updates) . " WHERE `TicketActionItemId` = " . $d->filter($this->TicketActionItemId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketActionItemId':
            case 'TicketId':
            case 'ActionItemId':
            case 'Completed':
            case 'CompletedByUserId':
            case 'CreatedByUserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Title':
            case 'Description':
            case 'DueDatetime':
            case 'Comment':
            case 'CompletedAt':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TicketActionItem` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TicketActionItem` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TicketActionItem();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TicketActionItem::$key");
        return $isUpdatable;
    }

    public function getTicketActionItemId(){
        return $this->TicketActionItemId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getActionItemId(){
        return $this->ActionItemId;
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getDescription(){
        return $this->Description;
    }

    public function getDueDatetime(){
        return $this->DueDatetime;
    }

    public function getComment(){
        return $this->Comment;
    }

    public function getCompleted(){
        return $this->Completed;
    }

    public function getCompletedByUserId(){
        return $this->CompletedByUserId;
    }

    public function getCompletedAt(){
        return $this->CompletedAt;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getCreatedByUserId(){
        return $this->CreatedByUserId;
    }

    public function getTicketActionItemIdAsInt(): int {
        return (int)$this->TicketActionItemId;
    }

    public function getTicketActionItemIdAsBool(): bool {
        return (bool)$this->TicketActionItemId;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getActionItemIdAsInt(): int {
        return (int)$this->ActionItemId;
    }

    public function getActionItemIdAsBool(): bool {
        return (bool)$this->ActionItemId;
    }

    public function getDueDatetimeAsDateTime(): DateTime {
        return ($this->DueDatetime instanceof DateTime) ? $this->DueDatetime : new DateTime($this->DueDatetime);
    }

    public function getCompletedAsInt(): int {
        return (int)$this->Completed;
    }

    public function getCompletedAsBool(): bool {
        return (bool)$this->Completed;
    }

    public function getCompletedByUserIdAsInt(): int {
        return (int)$this->CompletedByUserId;
    }

    public function getCompletedByUserIdAsBool(): bool {
        return (bool)$this->CompletedByUserId;
    }

    public function getCompletedAtAsDateTime(): DateTime {
        return ($this->CompletedAt instanceof DateTime) ? $this->CompletedAt : new DateTime($this->CompletedAt);
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function getCreatedByUserIdAsInt(): int {
        return (int)$this->CreatedByUserId;
    }

    public function getCreatedByUserIdAsBool(): bool {
        return (bool)$this->CreatedByUserId;
    }

    public function equals(?TicketActionItem $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
