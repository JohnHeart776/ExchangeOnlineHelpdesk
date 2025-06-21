<?php

class TicketStatus
{

    //Trait
    use TicketStatusTrait;

    public ?int $TicketStatusId = null;
    public ?string $Guid = null;
    public ?string $CreatedDatetime = null;
    public ?int $TicketId = null;
    public ?int $OldStatusId = null;
    public ?int $OldStatusIdIsFinal = null;
    public ?int $NewStatusId = null;
    public ?int $NewStatusIdIsFinal = null;
    public ?int $UserId = null;
    public ?string $Comment = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketStatusId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketStatusId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketStatusId FROM `TicketStatus` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketStatusId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketStatusId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketStatusId`, `Guid`, `CreatedDatetime`, `TicketId`, `OldStatusId`, `OldStatusIdIsFinal`, `NewStatusId`, `NewStatusIdIsFinal`, `UserId`, `Comment` FROM `TicketStatus` WHERE `TicketStatusId` = ".$d->filter($this->TicketStatusId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketStatusId = (int)$t['TicketStatusId'];
        $this->Guid = $t['Guid'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->TicketId = (int)$t['TicketId'];
        $this->OldStatusId = (int)$t['OldStatusId'];
        $this->OldStatusIdIsFinal = (int)$t['OldStatusIdIsFinal'];
        $this->NewStatusId = (int)$t['NewStatusId'];
        $this->NewStatusIdIsFinal = (int)$t['NewStatusIdIsFinal'];
        $this->UserId = (int)$t['UserId'];
        $this->Comment = $t['Comment'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`OldStatusId` = " . $d->filter((int)$this->OldStatusId);
        $updates[] = "`OldStatusIdIsFinal` = " . $d->filter((int)$this->OldStatusIdIsFinal);
        $updates[] = "`NewStatusId` = " . $d->filter((int)$this->NewStatusId);
        $updates[] = "`NewStatusIdIsFinal` = " . $d->filter((int)$this->NewStatusIdIsFinal);
        $updates[] = "`UserId` = " . $d->filter((int)$this->UserId);
        $updates[] = "`Comment` = \"" . $d->filter($this->Comment) . "\"";
        $_q = "UPDATE `TicketStatus` SET " . implode(", ", $updates) . " WHERE `TicketStatusId` = " . $d->filter($this->TicketStatusId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketStatusId':
            case 'TicketId':
            case 'OldStatusId':
            case 'OldStatusIdIsFinal':
            case 'NewStatusId':
            case 'NewStatusIdIsFinal':
            case 'UserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'CreatedDatetime':
            case 'Comment':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TicketStatus` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TicketStatus` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TicketStatus();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TicketStatus::$key");
        return $isUpdatable;
    }

    public function getTicketStatusId(){
        return $this->TicketStatusId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getOldStatusId(){
        return $this->OldStatusId;
    }

    public function getOldStatusIdIsFinal(){
        return $this->OldStatusIdIsFinal;
    }

    public function getNewStatusId(){
        return $this->NewStatusId;
    }

    public function getNewStatusIdIsFinal(){
        return $this->NewStatusIdIsFinal;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getComment(){
        return $this->Comment;
    }

    public function getTicketStatusIdAsInt(): int {
        return (int)$this->TicketStatusId;
    }

    public function getTicketStatusIdAsBool(): bool {
        return (bool)$this->TicketStatusId;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getOldStatusIdAsInt(): int {
        return (int)$this->OldStatusId;
    }

    public function getOldStatusIdAsBool(): bool {
        return (bool)$this->OldStatusId;
    }

    public function getOldStatusIdIsFinalAsInt(): int {
        return (int)$this->OldStatusIdIsFinal;
    }

    public function getOldStatusIdIsFinalAsBool(): bool {
        return (bool)$this->OldStatusIdIsFinal;
    }

    public function getNewStatusIdAsInt(): int {
        return (int)$this->NewStatusId;
    }

    public function getNewStatusIdAsBool(): bool {
        return (bool)$this->NewStatusId;
    }

    public function getNewStatusIdIsFinalAsInt(): int {
        return (int)$this->NewStatusIdIsFinal;
    }

    public function getNewStatusIdIsFinalAsBool(): bool {
        return (bool)$this->NewStatusIdIsFinal;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function equals(?TicketStatus $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
