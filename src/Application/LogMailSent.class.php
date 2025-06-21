<?php

class LogMailSent
{

    //Trait
    use LogMailSentTrait;

    public ?int $LogMailSentId = null;
    public ?string $Guid = null;
    public ?int $UserId = null;
    public ?string $Recipient = null;
    public ?string $Subject = null;
    public ?string $Body = null;
    public ?string $Created = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->LogMailSentId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->LogMailSentId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT LogMailSentId FROM `LogMailSent` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['LogMailSentId'];
    }

    public function isValid(): bool {
        return (bool)$this->LogMailSentId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `LogMailSentId`, `Guid`, `UserId`, `Recipient`, `Subject`, `Body`, `Created` FROM `LogMailSent` WHERE `LogMailSentId` = ".$d->filter($this->LogMailSentId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->LogMailSentId = (int)$t['LogMailSentId'];
        $this->Guid = $t['Guid'];
        $this->UserId = (int)$t['UserId'];
        $this->Recipient = $t['Recipient'];
        $this->Subject = $t['Subject'];
        $this->Body = $t['Body'];
        $this->Created = $t['Created'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`UserId` = " . $d->filter((int)$this->UserId);
        $updates[] = "`Recipient` = \"" . $d->filter($this->Recipient) . "\"";
        $updates[] = "`Subject` = \"" . $d->filter($this->Subject) . "\"";
        $updates[] = "`Body` = \"" . $d->filter($this->Body) . "\"";
        $updates[] = "`Created` = \"" . $d->filter(($this->Created instanceof DateTime ? $this->Created->format("Y-m-d H:i:s") : $this->Created)) . "\"";
        $_q = "UPDATE `LogMailSent` SET " . implode(", ", $updates) . " WHERE `LogMailSentId` = " . $d->filter($this->LogMailSentId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'LogMailSentId':
            case 'UserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Recipient':
            case 'Subject':
            case 'Body':
            case 'Created':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `LogMailSent` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `LogMailSent` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::LogMailSent();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for LogMailSent::$key");
        return $isUpdatable;
    }

    public function getLogMailSentId(){
        return $this->LogMailSentId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getRecipient(){
        return $this->Recipient;
    }

    public function getSubject(){
        return $this->Subject;
    }

    public function getBody(){
        return $this->Body;
    }

    public function getCreated(){
        return $this->Created;
    }

    public function getLogMailSentIdAsInt(): int {
        return (int)$this->LogMailSentId;
    }

    public function getLogMailSentIdAsBool(): bool {
        return (bool)$this->LogMailSentId;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function getCreatedAsDateTime(): DateTime {
        return ($this->Created instanceof DateTime) ? $this->Created : new DateTime($this->Created);
    }

    public function equals(?LogMailSent $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
