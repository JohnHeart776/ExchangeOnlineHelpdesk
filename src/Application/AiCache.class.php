<?php

class AiCache
{

    //Trait
    use AiCacheTrait;

    public ?int $AiCacheId = null;
    public ?string $Guid = null;
    public ?string $Payload = null;
    public ?string $PayloadHash = null;
    public ?string $Response = null;
    public ?string $CreatedAt = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->AiCacheId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->AiCacheId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT AiCacheId FROM `AiCache` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['AiCacheId'];
    }

    public function isValid(): bool {
        return (bool)$this->AiCacheId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `AiCacheId`, `Guid`, `Payload`, `PayloadHash`, `Response`, `CreatedAt` FROM `AiCache` WHERE `AiCacheId` = ".$d->filter($this->AiCacheId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->AiCacheId = (int)$t['AiCacheId'];
        $this->Guid = $t['Guid'];
        $this->Payload = $t['Payload'];
        $this->PayloadHash = $t['PayloadHash'];
        $this->Response = $t['Response'];
        $this->CreatedAt = $t['CreatedAt'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Payload` = \"" . $d->filter($this->Payload) . "\"";
        $updates[] = "`PayloadHash` = \"" . $d->filter($this->PayloadHash) . "\"";
        $updates[] = "`Response` = \"" . $d->filter($this->Response) . "\"";
        $updates[] = "`CreatedAt` = \"" . $d->filter(($this->CreatedAt instanceof DateTime ? $this->CreatedAt->format("Y-m-d H:i:s") : $this->CreatedAt)) . "\"";
        $_q = "UPDATE `AiCache` SET " . implode(", ", $updates) . " WHERE `AiCacheId` = " . $d->filter($this->AiCacheId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'AiCacheId':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'Payload':
            case 'PayloadHash':
            case 'Response':
            case 'CreatedAt':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `AiCache` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `AiCache` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::AiCache();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for AiCache::$key");
        return $isUpdatable;
    }

    public function getAiCacheId(){
        return $this->AiCacheId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getPayload(){
        return $this->Payload;
    }

    public function getPayloadHash(){
        return $this->PayloadHash;
    }

    public function getResponse(){
        return $this->Response;
    }

    public function getCreatedAt(){
        return $this->CreatedAt;
    }

    public function getAiCacheIdAsInt(): int {
        return (int)$this->AiCacheId;
    }

    public function getAiCacheIdAsBool(): bool {
        return (bool)$this->AiCacheId;
    }

    public function getCreatedAtAsDateTime(): DateTime {
        return ($this->CreatedAt instanceof DateTime) ? $this->CreatedAt : new DateTime($this->CreatedAt);
    }

    public function equals(?AiCache $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
