<?php

enum EnumTicketCommentTextType: string
{
	case txt = 'txt';
	case html = 'html';

	public function toString(): string
	{
		return $this->value;
	}

	public static function fromString(string $value): self
	{
		return self::from($value);
	}

}