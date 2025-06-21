<?php

enum EnumTicketCommentFacility: string
{
	case system = 'system';
	case automatic = 'automatic';
	case user = 'user';
	case other = 'other';

	public function toString(): string
	{
		return $this->value;
	}

	public static function fromString(string $value): self
	{
		return self::from($value);
	}

}