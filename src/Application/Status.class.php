<?php

class Status
{

    //Trait
    use StatusTrait;

    public ?int $StatusId = null;
    public ?string $Guid = null;
    public ?string $InternalName = null;
    public ?string $Color = null;
    public ?string $PublicName = null;
    public ?string $Icon = null;
    public ?int $IsOpen = null;
    public ?int $IsFinal = null;
    public ?int $IsDefault = null;
    public ?int $IsDefaultAssignedStatus = null;
    public ?int $IsDefaultWorkingStatus = null;
    public ?int $IsDetaultWaitingForCustomerStatus = null;
    public ?int $IsDefaultCustomerReplyStatus = null;
    public ?int $IsDefaultClosedStatus = null;
    public ?int $IsDefaultSolvedStatus = null;
    public ?int $SortOrder = null;
    public ?int $CustomerNotificationTemplateId = null;
    public ?int $AgentNotificationTemplateId = null;
    public ?string $CreatedAt = null;
    public ?string $UpdatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->StatusId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->StatusId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT StatusId FROM `Status` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['StatusId'];
    }

    public function isValid(): bool {
        return (bool)$this->StatusId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `StatusId`, `Guid`, `InternalName`, `Color`, `PublicName`, `Icon`, `IsOpen`, `IsFinal`, `IsDefault`, `IsDefaultAssignedStatus`, `IsDefaultWorkingStatus`, `IsDetaultWaitingForCustomerStatus`, `IsDefaultCustomerReplyStatus`, `IsDefaultClosedStatus`, `IsDefaultSolvedStatus`, `SortOrder`, `CustomerNotificationTemplateId`, `AgentNotificationTemplateId`, `CreatedAt`, `UpdatedAt` FROM `Status` WHERE `StatusId` = ".$d->filter($this->StatusId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->StatusId = (int)$t['StatusId'];
        $this->Guid = $t['Guid'];
        $this->InternalName = $t['InternalName'];
        $this->Color = $t['Color'];
        $this->PublicName = $t['PublicName'];
        $this->Icon = $t['Icon'];
        $this->IsOpen = (int)$t['IsOpen'];
        $this->IsFinal = (int)$t['IsFinal'];
        $this->IsDefault = (int)$t['IsDefault'];
        $this->IsDefaultAssignedStatus = (int)$t['IsDefaultAssignedStatus'];
        $this->IsDefaultWorkingStatus = (int)$t['IsDefaultWorkingStatus'];
        $this->IsDetaultWaitingForCustomerStatus = (int)$t['IsDetaultWaitingForCustomerStatus'];
        $this->IsDefaultCustomerReplyStatus = (int)$t['IsDefaultCustomerReplyStatus'];
        $this->IsDefaultClosedStatus = (int)$t['IsDefaultClosedStatus'];
        $this->IsDefaultSolvedStatus = (int)$t['IsDefaultSolvedStatus'];
        $this->SortOrder = (int)$t['SortOrder'];
        $this->CustomerNotificationTemplateId = (int)$t['CustomerNotificationTemplateId'];
        $this->AgentNotificationTemplateId = (int)$t['AgentNotificationTemplateId'];
        $this->CreatedAt = $t['CreatedAt'];
        $this->UpdatedAt = $t['UpdatedAt'];
        return $this;
    }

    public function save(): bool {
        $this->UpdatedAt = date("Y-m-d H:i:s");
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`InternalName` = \"" . $d->filter($this->InternalName) . "\"";
        $updates[] = "`Color` = \"" . $d->filter($this->Color) . "\"";
        $updates[] = "`PublicName` = \"" . $d->filter($this->PublicName) . "\"";
        $updates[] = "`Icon` = \"" . $d->filter($this->Icon) . "\"";
        $updates[] = "`IsOpen` = " . $d->filter((int)$this->IsOpen);
        $updates[] = "`IsFinal` = " . $d->filter((int)$this->IsFinal);
        $updates[] = "`IsDefault` = " . $d->filter((int)$this->IsDefault);
        $updates[] = "`IsDefaultAssignedStatus` = " . $d->filter((int)$this->IsDefaultAssignedStatus);
        $updates[] = "`IsDefaultWorkingStatus` = " . $d->filter((int)$this->IsDefaultWorkingStatus);
        $updates[] = "`IsDetaultWaitingForCustomerStatus` = " . $d->filter((int)$this->IsDetaultWaitingForCustomerStatus);
        $updates[] = "`IsDefaultCustomerReplyStatus` = " . $d->filter((int)$this->IsDefaultCustomerReplyStatus);
        $updates[] = "`IsDefaultClosedStatus` = " . $d->filter((int)$this->IsDefaultClosedStatus);
        $updates[] = "`IsDefaultSolvedStatus` = " . $d->filter((int)$this->IsDefaultSolvedStatus);
        $updates[] = "`SortOrder` = " . $d->filter((int)$this->SortOrder);
        $updates[] = "`CustomerNotificationTemplateId` = " . $d->filter((int)$this->CustomerNotificationTemplateId);
        $updates[] = "`AgentNotificationTemplateId` = " . $d->filter((int)$this->AgentNotificationTemplateId);
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $updates[] = "`UpdatedAt` = \"" . $d->filter(($this->UpdatedAt instanceof DateTime ? $this->UpdatedAt->format("Y-m-d H:i:s") : $this->UpdatedAt)) . "\"";
        $_q = "UPDATE `Status` SET " . implode(", ", $updates) . " WHERE `StatusId` = " . $d->filter($this->StatusId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'StatusId':
            case 'IsOpen':
            case 'IsFinal':
            case 'IsDefault':
            case 'IsDefaultAssignedStatus':
            case 'IsDefaultWorkingStatus':
            case 'IsDetaultWaitingForCustomerStatus':
            case 'IsDefaultCustomerReplyStatus':
            case 'IsDefaultClosedStatus':
            case 'IsDefaultSolvedStatus':
            case 'SortOrder':
            case 'CustomerNotificationTemplateId':
            case 'AgentNotificationTemplateId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'InternalName':
            case 'Color':
            case 'PublicName':
            case 'Icon':
            case 'CreatedAt':
            case 'UpdatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Status` SET `$key` = $value, `UpdatedAt` = \"" . date("Y-m-d H:i:s") . "\" WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Status` SET `$key` = NULL, `UpdatedAt` = \"" . date("Y-m-d H:i:s") . "\" WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Status();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Status::$key");
        return $isUpdatable;
    }

    public function getStatusId(){
        return $this->StatusId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getInternalName(){
        return $this->InternalName;
    }

    public function getColor(){
        return $this->Color;
    }

    public function getPublicName(){
        return $this->PublicName;
    }

    public function getIcon(){
        return $this->Icon;
    }

    public function getIsOpen(){
        return $this->IsOpen;
    }

    public function getIsFinal(){
        return $this->IsFinal;
    }

    public function getIsDefault(){
        return $this->IsDefault;
    }

    public function getIsDefaultAssignedStatus(){
        return $this->IsDefaultAssignedStatus;
    }

    public function getIsDefaultWorkingStatus(){
        return $this->IsDefaultWorkingStatus;
    }

    public function getIsDetaultWaitingForCustomerStatus(){
        return $this->IsDetaultWaitingForCustomerStatus;
    }

    public function getIsDefaultCustomerReplyStatus(){
        return $this->IsDefaultCustomerReplyStatus;
    }

    public function getIsDefaultClosedStatus(){
        return $this->IsDefaultClosedStatus;
    }

    public function getIsDefaultSolvedStatus(){
        return $this->IsDefaultSolvedStatus;
    }

    public function getSortOrder(){
        return $this->SortOrder;
    }

    public function getCustomerNotificationTemplateId(){
        return $this->CustomerNotificationTemplateId;
    }

    public function getAgentNotificationTemplateId(){
        return $this->AgentNotificationTemplateId;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getUpdatedAt(){
        return $this->UpdatedAt;
    }

    public function getStatusIdAsInt(): int {
        return (int)$this->StatusId;
    }

    public function getStatusIdAsBool(): bool {
        return (bool)$this->StatusId;
    }

    public function getIsOpenAsInt(): int {
        return (int)$this->IsOpen;
    }

    public function getIsOpenAsBool(): bool {
        return (bool)$this->IsOpen;
    }

    public function getIsFinalAsInt(): int {
        return (int)$this->IsFinal;
    }

    public function getIsFinalAsBool(): bool {
        return (bool)$this->IsFinal;
    }

    public function getIsDefaultAsInt(): int {
        return (int)$this->IsDefault;
    }

    public function getIsDefaultAsBool(): bool {
        return (bool)$this->IsDefault;
    }

    public function getIsDefaultAssignedStatusAsInt(): int {
        return (int)$this->IsDefaultAssignedStatus;
    }

    public function getIsDefaultAssignedStatusAsBool(): bool {
        return (bool)$this->IsDefaultAssignedStatus;
    }

    public function getIsDefaultWorkingStatusAsInt(): int {
        return (int)$this->IsDefaultWorkingStatus;
    }

    public function getIsDefaultWorkingStatusAsBool(): bool {
        return (bool)$this->IsDefaultWorkingStatus;
    }

    public function getIsDetaultWaitingForCustomerStatusAsInt(): int {
        return (int)$this->IsDetaultWaitingForCustomerStatus;
    }

    public function getIsDetaultWaitingForCustomerStatusAsBool(): bool {
        return (bool)$this->IsDetaultWaitingForCustomerStatus;
    }

    public function getIsDefaultCustomerReplyStatusAsInt(): int {
        return (int)$this->IsDefaultCustomerReplyStatus;
    }

    public function getIsDefaultCustomerReplyStatusAsBool(): bool {
        return (bool)$this->IsDefaultCustomerReplyStatus;
    }

    public function getIsDefaultClosedStatusAsInt(): int {
        return (int)$this->IsDefaultClosedStatus;
    }

    public function getIsDefaultClosedStatusAsBool(): bool {
        return (bool)$this->IsDefaultClosedStatus;
    }

    public function getIsDefaultSolvedStatusAsInt(): int {
        return (int)$this->IsDefaultSolvedStatus;
    }

    public function getIsDefaultSolvedStatusAsBool(): bool {
        return (bool)$this->IsDefaultSolvedStatus;
    }

    public function getSortOrderAsInt(): int {
        return (int)$this->SortOrder;
    }

    public function getSortOrderAsBool(): bool {
        return (bool)$this->SortOrder;
    }

    public function getCustomerNotificationTemplateIdAsInt(): int {
        return (int)$this->CustomerNotificationTemplateId;
    }

    public function getCustomerNotificationTemplateIdAsBool(): bool {
        return (bool)$this->CustomerNotificationTemplateId;
    }

    public function getAgentNotificationTemplateIdAsInt(): int {
        return (int)$this->AgentNotificationTemplateId;
    }

    public function getAgentNotificationTemplateIdAsBool(): bool {
        return (bool)$this->AgentNotificationTemplateId;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function getUpdatedAtAsDateTime(): DateTime {
        return ($this->UpdatedAt instanceof DateTime) ? $this->UpdatedAt : new DateTime($this->UpdatedAt);
    }

    public function equals(?Status $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
