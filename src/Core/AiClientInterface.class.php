<?php

interface AiClientInterface
{

	public function getResponseString(string $userPrompt): string;

	public function getSystemRole(): array;

	public function getInitialPayload(bool $includeSystemRole = true): array;

	public function getResponseForMessageArray(array $messages);

}

