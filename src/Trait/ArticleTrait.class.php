<?php

trait ArticleTrait
{
	use JsonSerializableTrait;
	use BooleanCheckTrait;

	public function getLink()
	{
		return "/article/" . $this->getSlug();
	}

	/**
	 * @param string $slug
	 * @return Article|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function bySlug(string $slug): ?Article
	{
		return ArticleController::searchOneBy("Slug", $slug);
	}

	public function toJsonObject(): array
	{
		return array_merge($this->getBaseJsonFields(), [
			"title" => $this->getTitle(),
			"slug" => $this->getSlug(),
			"link" => $this->getLink(),
		]);
	}

	public function getAccessLevelIsPublic(): bool
	{
		return $this->isFieldEqualTo('getAccessLevel', 'Public');
	}

	public function getAccessLevelIsAgent(): bool
	{
		return $this->isFieldEqualTo('getAccessLevel', 'Agent');
	}

	public function getContentForTinyMce(): string
	{
		return htmlspecialchars($this->getContent() ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8', false);
	}

	public function getTitleForTinyMce()
	{
		return htmlspecialchars($this->getTitle() ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8', false);
	}

	/**
	 * @return DateEta
	 */
	public function getUpdatedAtAsDateEta(): DateEta
	{
		return new DateEta($this->getUpdatedAtDatetimeAsDateTime());
	}

	/**
	 * @return true
	 * @throws \Database\DatabaseQueryException
	 */
	public function delete(): true
	{
		global $d;
		$_q = "DELETE FROM `Article` WHERE `Guid` = '" . $this->getGuid() . "';";
		$d->query($_q);
		return true;
	}

}
