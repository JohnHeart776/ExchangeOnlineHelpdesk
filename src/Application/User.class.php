<?php

class User
{

    //Trait
    use UserTrait;

    public ?int $UserId = null;
    public ?string $Guid = null;
    public ?int $Enabled = null;
    public ?string $TenantId = null;
    public ?string $AzureObjectId = null;
    public ?string $Upn = null;
    public ?string $DisplayName = null;
    public ?string $Name = null;
    public ?string $Surname = null;
    public ?string $Title = null;
    public ?string $Mail = null;
    public ?string $Telephone = null;
    public ?string $OfficeLocation = null;
    public ?string $CompanyName = null;
    public ?string $MobilePhone = null;
    public ?string $BusinessPhones = null;
    public ?int $AccountEnabled = null;
    public ?string $UserRole = null;
    public ?string $LastLogin = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->UserId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->UserId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT UserId FROM `User` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['UserId'];
    }

    public function isValid(): bool {
        return (bool)$this->UserId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `UserId`, `Guid`, `Enabled`, `TenantId`, `AzureObjectId`, `Upn`, `DisplayName`, `Name`, `Surname`, `Title`, `Mail`, `Telephone`, `OfficeLocation`, `CompanyName`, `MobilePhone`, `BusinessPhones`, `AccountEnabled`, `UserRole`, `LastLogin` FROM `User` WHERE `UserId` = ".$d->filter($this->UserId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->UserId = (int)$t['UserId'];
        $this->Guid = $t['Guid'];
        $this->Enabled = (int)$t['Enabled'];
        $this->TenantId = $t['TenantId'];
        $this->AzureObjectId = $t['AzureObjectId'];
        $this->Upn = $t['Upn'];
        $this->DisplayName = $t['DisplayName'];
        $this->Name = $t['Name'];
        $this->Surname = $t['Surname'];
        $this->Title = $t['Title'];
        $this->Mail = $t['Mail'];
        $this->Telephone = $t['Telephone'];
        $this->OfficeLocation = $t['OfficeLocation'];
        $this->CompanyName = $t['CompanyName'];
        $this->MobilePhone = $t['MobilePhone'];
        $this->BusinessPhones = $t['BusinessPhones'];
        $this->AccountEnabled = (int)$t['AccountEnabled'];
        $this->UserRole = $t['UserRole'];
        $this->LastLogin = $t['LastLogin'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Enabled` = " . $d->filter((int)$this->Enabled);
        $updates[] = "`TenantId` = \"" . $d->filter($this->TenantId) . "\"";
        $updates[] = "`AzureObjectId` = \"" . $d->filter($this->AzureObjectId) . "\"";
        $updates[] = "`Upn` = \"" . $d->filter($this->Upn) . "\"";
        $updates[] = "`DisplayName` = \"" . $d->filter($this->DisplayName) . "\"";
        $updates[] = "`Name` = \"" . $d->filter($this->Name) . "\"";
        $updates[] = "`Surname` = \"" . $d->filter($this->Surname) . "\"";
        $updates[] = "`Title` = \"" . $d->filter($this->Title) . "\"";
        $updates[] = "`Mail` = \"" . $d->filter($this->Mail) . "\"";
        $updates[] = "`Telephone` = \"" . $d->filter($this->Telephone) . "\"";
        $updates[] = "`OfficeLocation` = \"" . $d->filter($this->OfficeLocation) . "\"";
        $updates[] = "`CompanyName` = \"" . $d->filter($this->CompanyName) . "\"";
        $updates[] = "`MobilePhone` = \"" . $d->filter($this->MobilePhone) . "\"";
        $updates[] = "`BusinessPhones` = \"" . $d->filter($this->BusinessPhones) . "\"";
        $updates[] = "`AccountEnabled` = " . $d->filter((int)$this->AccountEnabled);
        $updates[] = "`UserRole` = \"" . $d->filter($this->UserRole) . "\"";
        $updates[] = "`LastLogin` = \"" . $d->filter(($this->LastLogin instanceof DateTime ? $this->LastLogin->format("Y-m-d H:i:s") : $this->LastLogin)) . "\"";
        $_q = "UPDATE `User` SET " . implode(", ", $updates) . " WHERE `UserId` = " . $d->filter($this->UserId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'UserId':
            case 'Enabled':
            case 'AccountEnabled':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'TenantId':
            case 'AzureObjectId':
            case 'Upn':
            case 'DisplayName':
            case 'Name':
            case 'Surname':
            case 'Title':
            case 'Mail':
            case 'Telephone':
            case 'OfficeLocation':
            case 'CompanyName':
            case 'MobilePhone':
            case 'BusinessPhones':
            case 'UserRole':
            case 'LastLogin':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `User` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `User` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::User();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for User::$key");
        return $isUpdatable;
    }

    public function getUserId(){
        return $this->UserId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getEnabled(){
        return $this->Enabled;
    }

    public function getTenantId(){
        return $this->TenantId;
    }

    public function getAzureObjectId(){
        return $this->AzureObjectId;
    }

    public function getUpn(){
        return $this->Upn;
    }

    public function getDisplayName(){
        return $this->DisplayName;
    }

    public function getName(){
        return $this->Name;
    }

    public function getSurname(){
        return $this->Surname;
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getMail(){
        return $this->Mail;
    }

    public function getTelephone(){
        return $this->Telephone;
    }

    public function getOfficeLocation(){
        return $this->OfficeLocation;
    }

    public function getCompanyName(){
        return $this->CompanyName;
    }

    public function getMobilePhone(){
        return $this->MobilePhone;
    }

    public function getBusinessPhones(){
        return $this->BusinessPhones;
    }

    public function getAccountEnabled(){
        return $this->AccountEnabled;
    }

    public function getUserRole(){
        return $this->UserRole;
    }

    public function getLastLogin(){
        return $this->LastLogin;
    }

    public function getUserIdAsInt(): int {
        return (int)$this->UserId;
    }

    public function getUserIdAsBool(): bool {
        return (bool)$this->UserId;
    }

    public function getEnabledAsInt(): int {
        return (int)$this->Enabled;
    }

    public function getEnabledAsBool(): bool {
        return (bool)$this->Enabled;
    }

    public function getAccountEnabledAsInt(): int {
        return (int)$this->AccountEnabled;
    }

    public function getAccountEnabledAsBool(): bool {
        return (bool)$this->AccountEnabled;
    }

    public function getLastLoginAsDateTime(): DateTime {
        return ($this->LastLogin instanceof DateTime) ? $this->LastLogin : new DateTime($this->LastLogin);
    }

    public function equals(?User $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
