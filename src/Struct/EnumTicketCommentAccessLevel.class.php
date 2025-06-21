<?php

enum EnumTicketCommentAccessLevel: string
{
	case Internal = 'Internal';
	case Public = 'Public';

	public function toString(): string
	{
		return $this->value;
	}

	public static function fromString(string $value): self
	{
		return self::from($value);
	}

}