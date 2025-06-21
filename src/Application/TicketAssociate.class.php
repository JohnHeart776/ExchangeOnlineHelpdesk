<?php

class TicketAssociate
{

    //Trait
    use TicketAssociateTrait;

    public ?int $TicketAssociateId = null;
    public ?string $Guid = null;
    public ?int $TicketId = null;
    public ?int $OrganizationUserId = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->TicketAssociateId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->TicketAssociateId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT TicketAssociateId FROM `TicketAssociate` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['TicketAssociateId'];
    }

    public function isValid(): bool {
        return (bool)$this->TicketAssociateId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `TicketAssociateId`, `Guid`, `TicketId`, `OrganizationUserId` FROM `TicketAssociate` WHERE `TicketAssociateId` = ".$d->filter($this->TicketAssociateId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->TicketAssociateId = (int)$t['TicketAssociateId'];
        $this->Guid = $t['Guid'];
        $this->TicketId = (int)$t['TicketId'];
        $this->OrganizationUserId = (int)$t['OrganizationUserId'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`OrganizationUserId` = " . $d->filter((int)$this->OrganizationUserId);
        $_q = "UPDATE `TicketAssociate` SET " . implode(", ", $updates) . " WHERE `TicketAssociateId` = " . $d->filter($this->TicketAssociateId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'TicketAssociateId':
            case 'TicketId':
            case 'OrganizationUserId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `TicketAssociate` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `TicketAssociate` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::TicketAssociate();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for TicketAssociate::$key");
        return $isUpdatable;
    }

    public function getTicketAssociateId(){
        return $this->TicketAssociateId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getOrganizationUserId(){
        return $this->OrganizationUserId;
    }

    public function getTicketAssociateIdAsInt(): int {
        return (int)$this->TicketAssociateId;
    }

    public function getTicketAssociateIdAsBool(): bool {
        return (bool)$this->TicketAssociateId;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getOrganizationUserIdAsInt(): int {
        return (int)$this->OrganizationUserId;
    }

    public function getOrganizationUserIdAsBool(): bool {
        return (bool)$this->OrganizationUserId;
    }

    public function equals(?TicketAssociate $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
