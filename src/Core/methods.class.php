<?php

class methods
{

	public static function getAbsoluteDomainLink(bool $trailingSlash = false): string
	{
		$domain = Config::getConfigValueFor("site.domain");
		return "https://" . $domain . ($trailingSlash ? "/" : "");
	}

	public static function sanitize(?string $value): float|bool|int|string
	{
		if ($value === null)
			return "";

		$value = strip_tags($value);
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		$value = trim($value);

		if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
			return (int)$value;
		}

		if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
			return (float)$value;
		}

		if (filter_var($value, FILTER_VALIDATE_BOOL) !== false) {
			return (bool)$value;
		}

		return $value;

	}

	public static function snapshotCurrentUserData()
	{
		return [
			"get" => $_GET,
			"post" => $_POST,
			"request" => $_REQUEST,
			"server" => $_SERVER,
		];
	}


	public static function getLatestGitHead(): string
	{
		$file = __DIR__ . "/../../.git/logs/HEAD";
		if (!file_exists($file))
			return uniqid();

		$file = trim(file_get_contents($file));
		$file = explode(PHP_EOL, $file);
		$file = array_reverse(array_values($file))[0];


		$id = explode(" ", $file)[1];
		return $id;
	}

	public static function bringToCustomerFront()
	{
		Header("Location: /");
		die();
	}

}

