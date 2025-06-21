<?php

namespace Struct;

class MailRecipient
{
	public string $name;
	public string $mail;

	public function __construct(string $name, string $mail)
	{
		$this->name = $name;
		$this->mail = $mail;
	}

	public function getDisplayName(): string
	{
		return $this->name . " < " . $this->mail . " >";
	}

	public function __toString(): string
	{
		return $this->getDisplayName();
	}
}