<?php

class Article
{

    //Trait
    use ArticleTrait;

    public ?int $ArticleId = null;
    public ?string $Guid = null;
    public ?int $Published = null;
    public ?string $AccessLevel = null;
    public ?string $CreatedDatetime = null;
    public ?string $UpdatedAtDatetime = null;
    public ?string $Slug = null;
    public ?string $Title = null;
    public ?string $Content = null;

    public function __construct($key) {
        if (is_int($key) && $key != 0) {
            $this->ArticleId = (int)$key;
        }
        if (guid::is_guid($key)) {
            $this->ArticleId = self::resolveGuidToId($key);
        }
        $this->spawn();
    }

    public static function resolveGuidToId(string $guid): int {
       global $d;
       $_q = "SELECT ArticleId FROM `Article` WHERE `Guid` = \"".$d->filter($guid)."\" LIMIT 1";
       $t = $d->get($_q, true);
       return (int)$t['ArticleId'];
    }

    public function isValid(): bool {
        return (bool)$this->ArticleId;
    }

    public function spawn(): ?self {
        if (!$this->isValid()) {
           return null;
        }
        global $d;
        $_q = "SELECT `ArticleId`, `Guid`, `Published`, `AccessLevel`, `CreatedDatetime`, `UpdatedAtDatetime`, `Slug`, `Title`, `Content` FROM `Article` WHERE `ArticleId` = ".$d->filter($this->ArticleId)." LIMIT 1";
        $t = $d->get($_q, true);
        if (empty($t)) {
            return null;
        }
        $this->ArticleId = (int)$t['ArticleId'];
        $this->Guid = $t['Guid'];
        $this->Published = (int)$t['Published'];
        $this->AccessLevel = $t['AccessLevel'];
        $this->CreatedDatetime = $t['CreatedDatetime'];
        $this->UpdatedAtDatetime = $t['UpdatedAtDatetime'];
        $this->Slug = $t['Slug'];
        $this->Title = $t['Title'];
        $this->Content = $t['Content'];
        return $this;
    }

    public function save(): bool {
        global $d;
        $updates = [];
        $updates[] = "`Guid` = \"" . $d->filter($this->Guid) . "\"";
        $updates[] = "`Published` = " . $d->filter((int)$this->Published);
        $updates[] = "`AccessLevel` = \"" . $d->filter($this->AccessLevel) . "\"";
        $updates[] = "`CreatedDatetime` = \"" . $d->filter(($this->CreatedDatetime instanceof DateTime ? $this->CreatedDatetime->format("Y-m-d H:i:s") : $this->CreatedDatetime)) . "\"";
        $updates[] = "`UpdatedAtDatetime` = \"" . $d->filter(($this->UpdatedAtDatetime instanceof DateTime ? $this->UpdatedAtDatetime->format("Y-m-d H:i:s") : $this->UpdatedAtDatetime)) . "\"";
        $updates[] = "`Slug` = \"" . $d->filter($this->Slug) . "\"";
        $updates[] = "`Title` = \"" . $d->filter($this->Title) . "\"";
        $updates[] = "`Content` = \"" . $d->filter($this->Content) . "\"";
        $_q = "UPDATE `Article` SET " . implode(", ", $updates) . " WHERE `ArticleId` = " . $d->filter($this->ArticleId) . " LIMIT 1";
        return $d->query($_q);
    }

    public function update($key, $value): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        switch ($key) {
            case 'ArticleId':
            case 'Published':
                $value = $d->filter((int)$value);
                break;

            case 'Guid':
            case 'AccessLevel':
            case 'CreatedDatetime':
            case 'UpdatedAtDatetime':
            case 'Slug':
            case 'Title':
            case 'Content':
                $value = "\"".$d->filter((string)$value)."\"";
                break;

            default:
                return false;
        }
        $_q = "UPDATE `Article` SET `$key` = $value WHERE `Guid` = \"{$this->Guid}\";";
        return $d->query($_q);
    }

    public function setNull($key): bool {
        if (!$this->isUpdateAllowedKey($key)) {
            return false;
        }
        global $d;
        $_q = "UPDATE `Article` SET `$key` = NULL WHERE `Guid` = \"{$this->Guid}\";";
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
        $a = \Updatable::Article();
        $isUpdatable = in_array($key, $a);
        if (!$isUpdatable)
            throw new Exception("Update not allowed for Article::$key");
        return $isUpdatable;
    }

    public function getArticleId(){
        return $this->ArticleId;
    }

    public function getGuid(){
        return $this->Guid;
    }

    public function getPublished(){
        return $this->Published;
    }

    public function getAccessLevel(){
        return $this->AccessLevel;
    }

    public function getCreatedDatetime(){
        return $this->CreatedDatetime;
    }

    public function getUpdatedAtDatetime(){
        return $this->UpdatedAtDatetime;
    }

    public function getSlug(){
        return $this->Slug;
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getContent(){
        return $this->Content;
    }

    public function getArticleIdAsInt(): int {
        return (int)$this->ArticleId;
    }

    public function getArticleIdAsBool(): bool {
        return (bool)$this->ArticleId;
    }

    public function getPublishedAsInt(): int {
        return (int)$this->Published;
    }

    public function getPublishedAsBool(): bool {
        return (bool)$this->Published;
    }

    public function getCreatedDatetimeAsDateTime(): DateTime {
        return ($this->CreatedDatetime instanceof DateTime) ? $this->CreatedDatetime : new DateTime($this->CreatedDatetime);
    }

    public function getUpdatedAtDatetimeAsDateTime(): DateTime {
        return ($this->UpdatedAtDatetime instanceof DateTime) ? $this->UpdatedAtDatetime : new DateTime($this->UpdatedAtDatetime);
    }

    public function equals(?Article $obj): bool {
        if ($obj === null) {
            return false;
        }
        return $this->Guid == $obj->Guid;
    }

}
