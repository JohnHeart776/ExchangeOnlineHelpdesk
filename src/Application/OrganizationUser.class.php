<?php

class OrganizationUser
{

    //Trait
    use OrganizationUserTrait;

    public ?int $OrganizationUserId = null;
    public ?string $Guid = null;
    public ?string $AzureObjectId = null;
    public ?string $DisplayName = null;
    public ?string $UserPrincipalName = null;
    public ?string $Mail = null;
    public ?string $GivenName = null;
    public ?string $Surname = null;
    public ?string $JobTitle = null;
    public ?string $Department = null;
    public ?string $MobilePhone = null;
    public ?string $OfficeLocation = null;
    public ?string $CompanyName = null;
    public ?string $BusinessPhones = null;
    public ?int $AccountEnabled = null;
    public ?string $EmployeeId = null;
    public ?string $SamAccountName = null;
    public ?string $Photo = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->OrganizationUserId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->OrganizationUserId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT OrganizationUserId FROM `OrganizationUser` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['OrganizationUserId'];
    }

    public function isValid(): bool {
        return (bool)$this->OrganizationUserId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `OrganizationUserId`, `Guid`, `AzureObjectId`, `DisplayName`, `UserPrincipalName`, `Mail`, `GivenName`, `Surname`, `JobTitle`, `Department`, `MobilePhone`, `OfficeLocation`, `CompanyName`, `BusinessPhones`, `AccountEnabled`, `EmployeeId`, `SamAccountName`, `Photo`, `CreatedAt` FROM `OrganizationUser` WHERE `OrganizationUserId` = ".$d->filter($this->OrganizationUserId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->OrganizationUserId = (int)$t['OrganizationUserId'];
        $this->Guid = $t['Guid'];
        $this->AzureObjectId = $t['AzureObjectId'];
        $this->DisplayName = $t['DisplayName'];
        $this->UserPrincipalName = $t['UserPrincipalName'];
        $this->Mail = $t['Mail'];
        $this->GivenName = $t['GivenName'];
        $this->Surname = $t['Surname'];
        $this->JobTitle = $t['JobTitle'];
        $this->Department = $t['Department'];
        $this->MobilePhone = $t['MobilePhone'];
        $this->OfficeLocation = $t['OfficeLocation'];
        $this->CompanyName = $t['CompanyName'];
        $this->BusinessPhones = $t['BusinessPhones'];
        $this->AccountEnabled = (int)$t['AccountEnabled'];
        $this->EmployeeId = $t['EmployeeId'];
        $this->SamAccountName = $t['SamAccountName'];
        $this->Photo = $t['Photo'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`AzureObjectId` = \"" . $d->filter($this->AzureObjectId) . "\"";
        $updates[] = "`DisplayName` = \"" . $d->filter($this->DisplayName) . "\"";
        $updates[] = "`UserPrincipalName` = \"" . $d->filter($this->UserPrincipalName) . "\"";
        $updates[] = "`Mail` = \"" . $d->filter($this->Mail) . "\"";
        $updates[] = "`GivenName` = \"" . $d->filter($this->GivenName) . "\"";
        $updates[] = "`Surname` = \"" . $d->filter($this->Surname) . "\"";
        $updates[] = "`JobTitle` = \"" . $d->filter($this->JobTitle) . "\"";
        $updates[] = "`Department` = \"" . $d->filter($this->Department) . "\"";
        $updates[] = "`MobilePhone` = \"" . $d->filter($this->MobilePhone) . "\"";
        $updates[] = "`OfficeLocation` = \"" . $d->filter($this->OfficeLocation) . "\"";
        $updates[] = "`CompanyName` = \"" . $d->filter($this->CompanyName) . "\"";
        $updates[] = "`BusinessPhones` = \"" . $d->filter($this->BusinessPhones) . "\"";
        $updates[] = "`AccountEnabled` = " . $d->filter((int)$this->AccountEnabled);
        $updates[] = "`EmployeeId` = \"" . $d->filter($this->EmployeeId) . "\"";
        $updates[] = "`SamAccountName` = \"" . $d->filter($this->SamAccountName) . "\"";
        $updates[] = "`Photo` = \"" . $d->filter($this->Photo) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `OrganizationUser` SET " . implode(", ", $updates) . " WHERE `OrganizationUserId` = " . $d->filter($this->OrganizationUserId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'OrganizationUserId':
            case 'AccountEnabled':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'AzureObjectId':
            case 'DisplayName':
            case 'UserPrincipalName':
            case 'Mail':
            case 'GivenName':
            case 'Surname':
            case 'JobTitle':
            case 'Department':
            case 'MobilePhone':
            case 'OfficeLocation':
            case 'CompanyName':
            case 'BusinessPhones':
            case 'EmployeeId':
            case 'SamAccountName':
            case 'Photo':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `OrganizationUser` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `OrganizationUser` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::OrganizationUser();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for OrganizationUser::$key");
        return $isUpdatable;
    }

    public function getOrganizationUserId(){
        return $this->OrganizationUserId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getAzureObjectId(){
        return $this->AzureObjectId;
    }

    public function getDisplayName(){
        return $this->DisplayName;
    }

    public function getUserPrincipalName(){
        return $this->UserPrincipalName;
    }

    public function getMail(){
        return $this->Mail;
    }

    public function getGivenName(){
        return $this->GivenName;
    }

    public function getSurname(){
        return $this->Surname;
    }

    public function getJobTitle(){
        return $this->JobTitle;
    }

    public function getDepartment(){
        return $this->Department;
    }

    public function getMobilePhone(){
        return $this->MobilePhone;
    }

    public function getOfficeLocation(){
        return $this->OfficeLocation;
    }

    public function getCompanyName(){
        return $this->CompanyName;
    }

    public function getBusinessPhones(){
        return $this->BusinessPhones;
    }

    public function getAccountEnabled(){
        return $this->AccountEnabled;
    }

    public function getEmployeeId(){
        return $this->EmployeeId;
    }

    public function getSamAccountName(){
        return $this->SamAccountName;
    }

    public function getPhoto(){
        return $this->Photo;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getOrganizationUserIdAsInt(): int {
        return (int)$this->OrganizationUserId;
    }

    public function getOrganizationUserIdAsBool(): bool {
        return (bool)$this->OrganizationUserId;
    }

    public function getAccountEnabledAsInt(): int {
        return (int)$this->AccountEnabled;
    }

    public function getAccountEnabledAsBool(): bool {
        return (bool)$this->AccountEnabled;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?OrganizationUser $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
