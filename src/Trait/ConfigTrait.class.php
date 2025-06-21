<?php

trait ConfigTrait
{
	/**
	 * @param string $key
	 * @return mixed
	 * @throws \Database\DatabaseQueryException
	 */
	public static function get(string $key, ?string $default = null): mixed
	{
		return self::getConfigValueFor($key, $default);
	}


	/**
	 * @param string $key
	 * @return mixed
	 * @throws \Database\DatabaseQueryException
	 */
	/**
	 * @param string $key
	 * @return mixed
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getConfigValueFor(string $key, ?string $default = null): mixed
	{
		$val = ConfigController::searchBy("Name", $key, true);
		if ($val === null) {
			return $default;
		}


		$val = $val->getValue();
		if ($val === "true") {
			return true;
		}
		if ($val === "false") {
			return false;
		}
		if ($val === "null") {
			return null;
		}
		return $val;
	}

	/**
	 * @return array
	 */
	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->getName(),
			"value" => $this->getValue(),
		];
	}

	public function getValueForEditable()
	{
		//encode html special chars
		return htmlspecialchars($this->getValue(), ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8', false);
	}

	/**
	 * Get the first part of a dot-separated string
	 * 
	 * @param string $dotSeparatedString The string to split
	 * @return string The first part of the string before the first dot
	 */
	public static function getFirstPartOfDotSeparatedString(string $dotSeparatedString): string
	{
		$parts = explode('.', $dotSeparatedString);
		return $parts[0];
	}

	/**
	 * Group configs by their first dot-separated name
	 * 
	 * @param array $configs Array of Config objects
	 * @return array Associative array with two keys: 'root_configs' and 'grouped_configs'
	 */
	public static function groupConfigsByFirstPart(array $configs): array
	{
		$grouped_configs = [];
		$root_configs = [];

		foreach ($configs as $config) {
			$name = $config->getName();
			if (strpos($name, '.') !== false) {
				$group_name = self::getFirstPartOfDotSeparatedString($name);
				if (!isset($grouped_configs[$group_name])) {
					$grouped_configs[$group_name] = [];
				}
				$grouped_configs[$group_name][] = $config;
			} else {
				$root_configs[] = $config;
			}
		}

		return [
			'root_configs' => $root_configs,
			'grouped_configs' => $grouped_configs
		];
	}

}
