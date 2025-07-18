<?php

/**
 * Check if a value is a decimal number (has fractional part)
 *
 * @param mixed $val Value to check
 * @return bool True if value is decimal, false otherwise
 */
function is_decimal($val): bool
{
	return is_numeric($val) && floor($val) != $val;
}

/**
 * Format a number with German locale formatting (comma as decimal separator, dot as thousands separator)
 *
 * @param float $number Number to format
 * @param int $decimals Number of decimal places (automatically set to 0 for integers)
 * @return string Formatted number string
 */
function formatNumber(float $number, int $decimals): string
{
	if (!is_decimal($number))
		$decimals = 0;

	$number_string = number_format($number, $decimals, ",", ".");
	return $number_string;
}

function getHttpXForwardedForHeader(): ?string
{
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

		if (str_contains($_SERVER['HTTP_X_FORWARDED_FOR'], ", ")) {
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			return $ips[0];
		}
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	return null;
}

/**
 * Get the client's IP address, considering proxy headers
 * Prioritizes X-Forwarded-For header over REMOTE_ADDR for proxy compatibility
 *
 * @return string Client IP address or empty string if not available
 */
function getClientIP(): string
{
	if (getHttpXForwardedForHeader()) {
		return getHttpXForwardedForHeader();
	}

	if (isset($_SERVER['REMOTE_ADDR'])) {
		return $_SERVER['REMOTE_ADDR'];
	}

	return "";
}

function isTestServer()
{
	if (getHttpHost() != "") {
		if (startsWith(getHttpHost(), "dev.")) {
			return true;
		}

		if (getHttpHost() == "localhost") {
			return true;
		}
	}

	return false;
}

function getCurrentLocation()
{
	if (isset($_SERVER["REQUEST_URI"]))
		return $_SERVER["REQUEST_URI"];
	return "/";
}


function slugify($text): string
{
	//replace umlauts
	$text = str_replace(
		["ä", "ö", "ü", "Ä", "Ö", "Ü"],
		["ae", "oe", "ue", "Ae", "Oe", "Ue"],
		$text
	);

	// replace non letter or digits by -
	$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	// trim
	$text = trim($text, '-');
	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	// lowercase
	$text = strtolower($text);
	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	if (empty($text)) {
		return 'n-a';
	}
	return $text;
}

/**
 * Parse a filename into its components (name and extension)
 *
 * @param string $filename The filename to parse
 * @return array{filename: string, extension: string, extension_undotted: string} Array containing filename parts
 */
function file_extension(string $filename): array
{
	$x = explode('.', $filename);
	$ext = end($x);
	$filenameSansExt = str_replace('.' . $ext, "", $filename);
	return [
		"filename" => $filenameSansExt,
		"extension" => '.' . $ext,
		"extension_undotted" => $ext,
	];
}

function weekDayNameGerman($int)
{
	switch ((int)$int) {
		case 0:
		case 7:
			return "Sonntag";
		case 1:
			return "Montag";
		case 2:
			return "Dienstag";
		case 3:
			return "Mittwoch";
		case 4:
			return "Donnerstag";
		case 5:
			return "Freitag";
		case 6:
			return "Samstag";
		default:
			return "Unbekannt";
	}
}

/**
 * This function is only needed because smarty does not
 * like (int) casts in phpstorm - so this function is
 * just to keep ide errors from popping up, sorry
 * @param $in
 * @return int
 */
function castToInt($in)
{
	return (int)$in;
}

function weekNameToInt($name)
{
	switch (strtolower($name)) {
		case "mon":
		case "montag":
			return 1;
		case "die":
		case "dienstag":
			return 2;
		case "mit":
		case "mittwoch":
			return 3;
		case "don":
		case "donnerstag":
			return 4;
		case "fre":
		case "freitag":
			return 5;
		case "sam":
		case "samstag":
			return 6;
		case "son":
		case "sonntag":
			return 7;
		default:
			return -1;
	}
}

function isCliInstance()
{
	return php_sapi_name() == "cli";
}

function getHttpHost(): string
{
	if (!isset($_SERVER["HTTP_HOST"]))
		return "";

	return $_SERVER["HTTP_HOST"];
}

/**
 * Get MIME type of a file
 *
 * @param string $filename Path to the file
 * @return string|false MIME type string or false if not determinable
 */
function getMimeType(string $filename): string|false
{
	$mimetype = false;

	if (function_exists('mime_content_type')) {
		$mimetype = mime_content_type($filename);
	}
	return $mimetype;
}

function jsonHeader()
{
	header('Content-Type: application/json');
}

function jsonStatus($status = true, $message = "", $data = false)
{
	jsonHeader();
	return json_encode(["status" => $status, "message" => $message, "data" => $data], JSON_THROW_ON_ERROR);

}

function guid()
{
	if (function_exists('com_create_guid') === true) {
		return strtolower(trim(com_create_guid(), '{}'));
	}
	//return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
	$data = openssl_random_pseudo_bytes(16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Get file age in seconds (time since last modification)
 *
 * @param string $path Path to the file
 * @return int File age in seconds, or 9999999 if file doesn't exist
 */
function fage(string $path): int
{
	if (!file_exists($path)) {
		return 9999999;
	}
	return (time() - filemtime($path));
}

function calcPercentageProgress($value, $min, $max)
{
	$diff = $value - $min;
	$scope = $max - $min;
	if ($scope == 0) {
		return 1;
	}
	return ($diff / $scope);
}

function getEnglishDayName(int $weekdayIndex)
{
	switch ($weekdayIndex) {
		case 1:
			return "Monday";
		case 2:
			return "Tuesday";
		case 3:
			return "Wednesday";
		case 4:
			return "Thursday";
		case 5:
			return "Friday";
		case 6:
			return "Saturday";
		case 7:
			return "Sunday";
		default:
			throw new Exception("Invalid Day Int");
	}
}

function getBase32LookupTable()
{
	return [
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
		'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
		'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
		'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
		'=',                                    // padding char
	];
}

function base32_decode($secret)
{
	if (empty($secret)) {
		return '';
	}
	$base32chars = getBase32LookupTable();
	$base32charsFlipped = array_flip($base32chars);
	$paddingCharCount = substr_count($secret, $base32chars[32]);
	$allowedValues = [6, 4, 3, 1, 0];
	if (!in_array($paddingCharCount, $allowedValues)) {
		return false;
	}
	for ($i = 0; $i < 4; $i++) {
		if ($paddingCharCount == $allowedValues[$i] &&
			substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])
		) {
			return false;
		}
	}
	$secret = str_replace('=', '', $secret);
	$secret = str_split($secret);
	$binaryString = "";
	$maxCount = count($secret);
	for ($i = 0; $i < $maxCount; $i = $i + 8) {
		$x = "";
		if (!in_array($secret[$i], $base32chars)) {
			return false;
		}
		for ($j = 0; $j < 8; $j++) {
			$x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
		}
		$eightBits = str_split($x, 8);
		$maxCount = count($eightBits);
		for ($z = 0; $z < $maxCount; $z++) {
			$binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : "";
		}
	}
	return $binaryString;
}

function base32_encode($secret, $padding = true)
{
	if (empty($secret)) {
		return '';
	}
	$base32chars = getBase32LookupTable();
	$secret = str_split($secret);
	$binaryString = "";
	$maxCount = count($secret);
	for ($i = 0; $i < $maxCount; $i++) {
		$binaryString .= str_pad(base_convert(ord($secret[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
	}
	$fiveBitBinaryArray = str_split($binaryString, 5);
	$base32 = "";
	$i = 0;
	while ($i < count($fiveBitBinaryArray)) {
		$base32 .= $base32chars[base_convert(str_pad($fiveBitBinaryArray[$i], 5, '0'), 2, 10)];
		$i++;
	}
	if ($padding && ($x = strlen($binaryString) % 40) != 0) {
		if ($x == 8) {
			$base32 .= str_repeat($base32chars[32], 6);
		} else if ($x == 16) {
			$base32 .= str_repeat($base32chars[32], 4);
		} else if ($x == 24) {
			$base32 .= str_repeat($base32chars[32], 3);
		} else if ($x == 32) {
			$base32 .= $base32chars[32];
		}
	}
	return $base32;
}



function totalHours($seconds)
{
	$hours = $seconds / 60 / 60;
	$minutes = ($hours - floor($hours)) * 60;
	return floor($hours) . 'h ' . str_pad($minutes, 2, 0, STR_PAD_LEFT) . 'm';
}

function days($days = 0)
{
	return (double)$days * hours(24);
}

function hours($hours)
{
	return (double)$hours * 60 * 60;
}

function br2nl($string)
{
	return preg_replace('/<br(\s*)?\/?>/i', "\n", $string);
}

function startsWith($haystack, $needle)
{
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle)
{
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function getWeekStartEnd($timestamp)
{
	$week_start = strtotime(date('o-\\WW', $timestamp) . " 00:00:00");
	$week_end = strtotime("+6 days 23:59:59", $week_start);
	return ["start" => $week_start, "end" => $week_end];
}

/**
 * Returns the total width in points of the string using the specified font and
 * size.
 * This is not the most efficient way to perform this calculation. I'm
 * concentrating optimization efforts on the upcoming layout manager class.
 * Similar calculations exist inside the layout manager class, but widths are
 * generally calculated only after determining line fragments.
 * @link http://devzone.zend.com/article/2525-Zend_Pdf-tutorial#comments-2535
 *
 * @param string                 $string
 * @param Zend_Pdf_Resource_Font $font
 * @param float                  $fontSize Font size in points
 *
 * @return float
 */
function widthForStringUsingFontSize($string, $font, $fontSize)
{
	//	$string = str_replace("ä", "ae", $string);
	$drawingString = @iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
	$characters = [];
	$maxCount = mb_strlen($drawingString);
	for ($i = 0; $i < $maxCount; $i++) {
		$characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
	}
	$glyphs = $font->glyphNumbersForCharacters($characters);
	$widths = $font->widthsForGlyphs($glyphs);
	$stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
	return $stringWidth;
}

/**
 * Check if a number is within a range (exclusive)
 *
 * @param int|float $int Number to check
 * @param int|float $min Minimum value (exclusive)
 * @param int|float $max Maximum value (exclusive)
 * @return bool True if number is within range, false otherwise
 */
function inRange(int|float $int, int|float $min, int|float $max): bool
{
	return ($min < $int && $int < $max);
}

/**
 * Calculate the inverse color of a given hex color
 *
 * @param string $color Hex color code (with or without #)
 * @return string Inverted hex color code with # prefix, or #000000 if invalid input
 */
function color_inverse(string $color): string
{
	$color = str_replace('#', '', $color);
	if (strlen($color) != 6) {
		return '#000000';
	}
	$rgb = '';
	for ($x = 0; $x < 3; $x++) {
		$c = 255 - hexdec(substr($color, (2 * $x), 2));
		$c = ($c < 0) ? 0 : dechex($c);
		$rgb .= (strlen($c) < 2) ? '0' . $c : $c;
	}
	return '#' . $rgb;
}

function reorderDays($tpl)
{
	$new = [];
	if (empty($tpl))
		return [];

	foreach ($tpl as $day => $hours) {
		$today = [];
		$later = [];

		foreach ($hours as $hour => $trainings) {
			$today[$hour] = [];
			foreach ($trainings as $training) {
				//echo "Comparing ".$training->start." as  ".(date('Y-m-d', $training->start)). " width ".date('Y-m-d', strtotime('next '.getDateDayName($day)))." \n";
				if (
					//TODAY
					date('Y-m-d', $training->start) == date('Y-m-d')
					||
					(date('Y-m-d', $training->start) == date('Y-m-d', strtotime('next ' . getDateDayName($day))) && !(date('Y-m-d', $training->start) == date('Y-m-d', strtotime('today + 1 week'))))
				) {
					$today[$hour][] = $training;
				} else //ansonsten später
				{
					$later[date('d.m. H:i', $training->start)][] = $training;
				}
			}
			if (empty($today[$hour])) {
				unset($today[$hour]);
			}
		}
		ksort($later);
		$new[$day]["today"] = $today;
		$new[$day]["later"] = $later;
	}
	//	die();
	return $new;
}

function renameDays($tpl)
{
	$new = [];
	foreach ($tpl as $day => $value) {
		$new[setDayNames($day)] = $value;
	}
	return $new;
}

function setDayNames($nr)
{
	switch ($nr) {
		case 0:
		case 7:
			return "Sonntag";
		case 1:
			return "Montag";
		case 2:
			return "Dienstag";
		case 3:
			return "Mittwoch";
		case 4:
			return "Donnerstag";
		case 5:
			return "Freitag";
		case 6 :
			return "Samstag";
		default:
			return "Wochentag";
	}

}

function getDateDayName($nr)
{
	switch ($nr) {
		case 0:
		case 7:
			return "sunday";
		case 1:
			return "monday";
		case 2:
			return "tuesday";
		case 3:
			return "wednesday";
		case 4:
			return "thursday";
		case 5:
			return "friday";
		case 6:
			return "saturday";
		default:
			return "unkown";
	}

}

function getNearestTraining(&$tpl, &$day_now)
{
	$nearest = null;
	foreach ($tpl as $day => $hour) {
		foreach ($hour as $trainings) {
			foreach ($trainings as $training) {
				if ($nearest == null
					|| $nearest->start > $training->start
				) {
					$day_now = $day;
					$nearest = $training;
				}
			}
		}
	}
	return $nearest;
}

function smarty_add(float $a, float $b): float
{
	return $a + $b;
}

function getBool(bool $b = true): bool
{
	return (bool)$b;
}

function getLatestGitHead()
{
	$file = __DIR__ . "/../.git/logs/HEAD";
	if (!file_exists($file))
		return uniqid();

	$file = trim(file_get_contents($file));
	$file = explode(PHP_EOL, $file);
	$file = array_reverse(array_values($file))[0];


	$id = explode(" ", $file)[1];
	return $id;
}


function smartyDie($param)
{
	die();
}

function getRandomHash()
{
	$data = openssl_random_pseudo_bytes(1024);
	$a = hash("sha3-256", $data);
	return $a;
}

function clean_input($input)
{
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlspecialchars($input);
	return $input;
}


function getReadableId(): string
{
	$words = [
		'Apple', 'Banana', 'Cherry', 'Guitar', 'Harmony', 'Jupiter', 'Keyboard', 'Lamp', 'Mountain',
		'Nectar', 'Ocean', 'Puzzle', 'Quantum', 'Rainbow', 'Satellite', 'Telescope', 'Umbrella',
		'Victory', 'Willow', 'Xylophone', 'Yellow', 'Zephyr', 'Amber', 'Blossom', 'Cactus',
		'Dolphin', 'Eclipse', 'Falcon', 'Galaxy', 'Horizon', 'Iceberg', 'Journal', 'Kite',
		'Lighthouse', 'Meteor', 'Nebula', 'Orchid', 'Panther', 'Quasar', 'Ripple', 'Sunbeam',
		'Twilight', 'Unicorn', 'Voyager', 'Wanderer', 'Xenon', 'Yacht', 'Zenith', 'Crescent', 'Phoenix',
	];

	$word = $words[array_rand($words)];                          // Pick a random word
	$number = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT); // Generate a 3-digit random number

	return "{$word}{$number}";
}

function dumpStructure($var, $indent = 0) {
	$indentStr = str_repeat("  ", $indent);

	if (is_array($var)) {
		echo $indentStr . "- array (" . count($var) . " item" . (count($var) === 1 ? "" : "s") . ")" . PHP_EOL;
		foreach ($var as $key => $value) {
			echo $indentStr . "  |- " . $key . PHP_EOL;
			dumpStructure($value, $indent + 2);
		}
	} elseif (is_object($var)) {
		$className = get_class($var);
		// Get public properties for inspection.
		$props = get_object_vars($var);
		echo $indentStr . "- object (" . $className . ", " . count($props) . " prop" . (count($props) === 1 ? "" : "s") . ")" . PHP_EOL;
		foreach ($props as $key => $value) {
			echo $indentStr . "  |- " . $key . PHP_EOL;
			dumpStructure($value, $indent + 2);
		}
	} elseif (is_string($var)) {
		$length = strlen($var);
		echo $indentStr . "-- string: content length: " . $length . " byte" . ($length === 1 ? "" : "s") . PHP_EOL;
	} elseif (is_int($var)) {
		echo $indentStr . "-- int: " . $var . PHP_EOL;
	} elseif (is_float($var)) {
		echo $indentStr . "-- float: " . $var . PHP_EOL;
	} elseif (is_bool($var)) {
		echo $indentStr . "-- bool: " . ($var ? "true" : "false") . PHP_EOL;
	} elseif (is_null($var)) {
		echo $indentStr . "-- null" . PHP_EOL;
	} else {
		echo $indentStr . "-- " . gettype($var) . PHP_EOL;
	}
}
