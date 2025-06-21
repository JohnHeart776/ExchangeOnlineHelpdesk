<?php

class base62
{

	public static function decode_string($encoded)
	{
		// Decode the base62 encoded string to a large number
		$number = self::decode($encoded);

		// Convert the large number back to the original string
		$output = '';
		while (bccomp($number, '0', 0) > 0) {
			$asciiValue = bcmod($number, '256');
			$output = chr((int)$asciiValue) . $output;
			$number = bcdiv($number, '256', 0);
		}

		return $output;
	}

	private static function decode($encoded)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base = 62;
		$length = strlen($encoded);
		$number = '0';

		for ($i = 0; $i < $length; $i++) {
			$character = $encoded[$i];
			$position = strpos($characters, $character);
			if ($position === false) {
				throw new \InvalidArgumentException("Invalid character in encoded string");
			}
			$number = bcmul($number, $base, 0);
			$number = bcadd($number, $position, 0);
		}

		return $number;
	}

	public static function encode_string($string)
	{
		// Convert the input string to a large number
		$number = '0';
		for ($i = 0; $i < strlen($string); $i++) {
			$asciiValue = ord($string[$i]);
			$number = bcadd(bcmul($number, '256', 0), $asciiValue, 0);
		}

		// Use the previously defined base62_encode function
		return self::encode($number);
	}

	public static function encode($number)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base = 62;
		$result = '';

		while (bccomp($number, '0', 0) > 0) {
			$remainder = bcmod($number, $base);
			$result = $characters[(int)$remainder] . $result;
			$number = bcdiv($number, $base, 0);
		}

		return $result;
	}

}
