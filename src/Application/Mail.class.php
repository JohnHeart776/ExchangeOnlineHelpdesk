<?php

class Mail
{

    //Trait
    use MailTrait;

    public ?int $MailId = null;
    public ?string $Guid = null;
    public ?string $SecureObjectHash = null;
    public ?string $SourceMailbox = null;
    public ?string $AzureId = null;
    public ?string $MessageId = null;
    public ?int $TicketId = null;
    public ?string $Subject = null;
    public ?string $SenderName = null;
    public ?string $SenderEmail = null;
    public ?string $FromName = null;
    public ?string $FromEmail = null;
    public ?string $ToRecipients = null;
    public ?string $CcRecipients = null;
    public ?string $BccRecipients = null;
    public ?string $Body = null;
    public ?string $BodyType = null;
    public ?string $ReceivedDatetime = null;
    public ?string $SentDatetime = null;
    public ?string $Importance = null;
    public ?string $ConversationId = null;
    public ?string $BodyPreview = null;
    public ?int $HasAttachments = null;
    public ?string $MailHeadersRaw = null;
    public ?string $HeadersJson = null;
    public ?string $AzureObject = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->MailId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->MailId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT MailId FROM `Mail` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['MailId'];
    }

    public function isValid(): bool {
        return (bool)$this->MailId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `MailId`, `Guid`, `SecureObjectHash`, `SourceMailbox`, `AzureId`, `MessageId`, `TicketId`, `Subject`, `SenderName`, `SenderEmail`, `FromName`, `FromEmail`, `ToRecipients`, `CcRecipients`, `BccRecipients`, `Body`, `BodyType`, `ReceivedDatetime`, `SentDatetime`, `Importance`, `ConversationId`, `BodyPreview`, `HasAttachments`, `MailHeadersRaw`, `HeadersJson`, `AzureObject`, `CreatedAt` FROM `Mail` WHERE `MailId` = ".$d->filter($this->MailId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->MailId = (int)$t['MailId'];
        $this->Guid = $t['Guid'];
        $this->SecureObjectHash = $t['SecureObjectHash'];
        $this->SourceMailbox = $t['SourceMailbox'];
        $this->AzureId = $t['AzureId'];
        $this->MessageId = $t['MessageId'];
        $this->TicketId = (int)$t['TicketId'];
        $this->Subject = $t['Subject'];
        $this->SenderName = $t['SenderName'];
        $this->SenderEmail = $t['SenderEmail'];
        $this->FromName = $t['FromName'];
        $this->FromEmail = $t['FromEmail'];
        $this->ToRecipients = $t['ToRecipients'];
        $this->CcRecipients = $t['CcRecipients'];
        $this->BccRecipients = $t['BccRecipients'];
        $this->Body = $t['Body'];
        $this->BodyType = $t['BodyType'];
        $this->ReceivedDatetime = $t['ReceivedDatetime'];
        $this->SentDatetime = $t['SentDatetime'];
        $this->Importance = $t['Importance'];
        $this->ConversationId = $t['ConversationId'];
        $this->BodyPreview = $t['BodyPreview'];
        $this->HasAttachments = (int)$t['HasAttachments'];
        $this->MailHeadersRaw = $t['MailHeadersRaw'];
        $this->HeadersJson = $t['HeadersJson'];
        $this->AzureObject = $t['AzureObject'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`SecureObjectHash` = \"" . $d->filter($this->SecureObjectHash) . "\"";
        $updates[] = "`SourceMailbox` = \"" . $d->filter($this->SourceMailbox) . "\"";
        $updates[] = "`AzureId` = \"" . $d->filter($this->AzureId) . "\"";
        $updates[] = "`MessageId` = \"" . $d->filter($this->MessageId) . "\"";
        $updates[] = "`TicketId` = " . $d->filter((int)$this->TicketId);
        $updates[] = "`Subject` = \"" . $d->filter($this->Subject) . "\"";
        $updates[] = "`SenderName` = \"" . $d->filter($this->SenderName) . "\"";
        $updates[] = "`SenderEmail` = \"" . $d->filter($this->SenderEmail) . "\"";
        $updates[] = "`FromName` = \"" . $d->filter($this->FromName) . "\"";
        $updates[] = "`FromEmail` = \"" . $d->filter($this->FromEmail) . "\"";
        $updates[] = "`ToRecipients` = \"" . $d->filter($this->ToRecipients) . "\"";
        $updates[] = "`CcRecipients` = \"" . $d->filter($this->CcRecipients) . "\"";
        $updates[] = "`BccRecipients` = \"" . $d->filter($this->BccRecipients) . "\"";
        $updates[] = "`Body` = \"" . $d->filter($this->Body) . "\"";
        $updates[] = "`BodyType` = \"" . $d->filter($this->BodyType) . "\"";
        $updates[] = "`ReceivedDatetime` = \"" . $d->filter(($this->ReceivedDatetime instanceof DateTime ? $this->ReceivedDatetime->format("Y-m-d H:i:s") : $this->ReceivedDatetime)) . "\"";
        $updates[] = "`SentDatetime` = \"" . $d->filter(($this->SentDatetime instanceof DateTime ? $this->SentDatetime->format("Y-m-d H:i:s") : $this->SentDatetime)) . "\"";
        $updates[] = "`Importance` = \"" . $d->filter($this->Importance) . "\"";
        $updates[] = "`ConversationId` = \"" . $d->filter($this->ConversationId) . "\"";
        $updates[] = "`BodyPreview` = \"" . $d->filter($this->BodyPreview) . "\"";
        $updates[] = "`HasAttachments` = " . $d->filter((int)$this->HasAttachments);
        $updates[] = "`MailHeadersRaw` = \"" . $d->filter($this->MailHeadersRaw) . "\"";
        $updates[] = "`HeadersJson` = \"" . $d->filter($this->HeadersJson) . "\"";
        $updates[] = "`AzureObject` = \"" . $d->filter($this->AzureObject) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `Mail` SET " . implode(", ", $updates) . " WHERE `MailId` = " . $d->filter($this->MailId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'MailId':
            case 'TicketId':
            case 'HasAttachments':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'SecureObjectHash':
            case 'SourceMailbox':
            case 'AzureId':
            case 'MessageId':
            case 'Subject':
            case 'SenderName':
            case 'SenderEmail':
            case 'FromName':
            case 'FromEmail':
            case 'ToRecipients':
            case 'CcRecipients':
            case 'BccRecipients':
            case 'Body':
            case 'BodyType':
            case 'ReceivedDatetime':
            case 'SentDatetime':
            case 'Importance':
            case 'ConversationId':
            case 'BodyPreview':
            case 'MailHeadersRaw':
            case 'HeadersJson':
            case 'AzureObject':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Mail` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Mail` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Mail();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Mail::$key");
        return $isUpdatable;
    }

    public function getMailId(){
        return $this->MailId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getSecureObjectHash(){
        return $this->SecureObjectHash;
    }

    public function getSourceMailbox(){
        return $this->SourceMailbox;
    }

    public function getAzureId(){
        return $this->AzureId;
    }

    public function getMessageId(){
        return $this->MessageId;
    }

    public function getTicketId(){
        return $this->TicketId;
    }

    public function getSubject(){
        return $this->Subject;
    }

    public function getSenderName(){
        return $this->SenderName;
    }

    public function getSenderEmail(){
        return $this->SenderEmail;
    }

    public function getFromName(){
        return $this->FromName;
    }

    public function getFromEmail(){
        return $this->FromEmail;
    }

    public function getToRecipients(){
        return $this->ToRecipients;
    }

    public function getCcRecipients(){
        return $this->CcRecipients;
    }

    public function getBccRecipients(){
        return $this->BccRecipients;
    }

    public function getBody(){
        return $this->Body;
    }

    public function getBodyType(){
        return $this->BodyType;
    }

    public function getReceivedDatetime(){
        return $this->ReceivedDatetime;
    }

    public function getSentDatetime(){
        return $this->SentDatetime;
    }

    public function getImportance(){
        return $this->Importance;
    }

    public function getConversationId(){
        return $this->ConversationId;
    }

    public function getBodyPreview(){
        return $this->BodyPreview;
    }

    public function getHasAttachments(){
        return $this->HasAttachments;
    }

    public function getMailHeadersRaw(){
        return $this->MailHeadersRaw;
    }

    public function getHeadersJson(){
        return $this->HeadersJson;
    }

    public function getAzureObject(){
        return $this->AzureObject;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getMailIdAsInt(): int {
        return (int)$this->MailId;
    }

    public function getMailIdAsBool(): bool {
        return (bool)$this->MailId;
    }

    public function getTicketIdAsInt(): int {
        return (int)$this->TicketId;
    }

    public function getTicketIdAsBool(): bool {
        return (bool)$this->TicketId;
    }

    public function getReceivedDatetimeAsDateTime(): DateTime {
        return ($this->ReceivedDatetime instanceof DateTime) ? $this->ReceivedDatetime : new DateTime($this->ReceivedDatetime);
    }

    public function getSentDatetimeAsDateTime(): DateTime {
        return ($this->SentDatetime instanceof DateTime) ? $this->SentDatetime : new DateTime($this->SentDatetime);
    }

    public function getHasAttachmentsAsInt(): int {
        return (int)$this->HasAttachments;
    }

    public function getHasAttachmentsAsBool(): bool {
        return (bool)$this->HasAttachments;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?Mail $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
