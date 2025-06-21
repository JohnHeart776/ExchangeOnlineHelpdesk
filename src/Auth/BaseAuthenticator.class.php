<?php

namespace Auth;

abstract class BaseAuthenticator
{
	protected ?string $accessToken = null;
	protected int $tokenExpires = 0;

	/**
	 * Proves an vaid Access Token
	 *
	 * @return string
	 * @throws \Exception
	 */
	abstract public function getAccessToken(): string;
}
