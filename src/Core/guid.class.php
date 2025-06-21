<?php

class guid
{
	public static function guid()
	{
		// Generate a UUID v4
		$data = openssl_random_pseudo_bytes(16);
		assert(strlen($data) == 16);

		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID
		$uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		return $uuid;
	}

	private static function uuid_v4_to_base62($uuid)
	{
		// Remove hyphens
		$uuid = str_replace('-', '', $uuid);

		// Convert hex to decimal manually
		$decimal = '0';
		$length = strlen($uuid);
		$base = '1';

		for ($i = $length - 1; $i >= 0; $i--) {
			$digit = hexdec($uuid[$i]);
			$decimal = bcadd($decimal, bcmul($digit, $base));
			$base = bcmul($base, '16');
		}

		// Convert decimal to base62
		$base62 = base62::encode($decimal);

		return $base62;
	}

	public static function guid62()
	{
		return self::uuid_v4_to_base62(self::guid());
	}


	public static function is_guid($str)
	{
		return preg_match('/^{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}}?$/', $str);
	}

	public static function extractGuid($str)
	{
		preg_match("/[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}/", $str, $matches);
		if (isset($matches[0])) {
			return $matches[0];
		}
		return false;
	}

}

