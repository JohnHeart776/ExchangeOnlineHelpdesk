<?php

/**
 * Helper class for day name conversions and utilities
 * Consolidates all day name related functionality from functions.php
 */
class DayNameHelper
{
    /**
     * Language constants
     */
    public const LANG_GERMAN = 'de';
    public const LANG_ENGLISH = 'en';

    /**
     * Day name mappings
     */
    private static array $dayNames = [
        self::LANG_GERMAN => [
            0 => 'Sonntag',
            1 => 'Montag',
            2 => 'Dienstag',
            3 => 'Mittwoch',
            4 => 'Donnerstag',
            5 => 'Freitag',
            6 => 'Samstag',
            7 => 'Sonntag', // Sunday can be 0 or 7
        ],
        self::LANG_ENGLISH => [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday', // Sunday can be 0 or 7
        ]
    ];

    /**
     * English day names for date functions (lowercase)
     */
    private static array $englishDateNames = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday', // Sunday can be 0 or 7
    ];

    /**
     * Week name to integer mappings
     */
    private static array $weekNameToInt = [
        'mon' => 1, 'montag' => 1,
        'die' => 2, 'dienstag' => 2,
        'mit' => 3, 'mittwoch' => 3,
        'don' => 4, 'donnerstag' => 4,
        'fre' => 5, 'freitag' => 5,
        'sam' => 6, 'samstag' => 6,
        'son' => 7, 'sonntag' => 7,
    ];

    /**
     * Get day name in specified language
     *
     * @param int $dayIndex Day index (0-7, where 0 and 7 are Sunday)
     * @param string $language Language code (use class constants)
     * @return string Day name in specified language
     * @throws InvalidArgumentException If day index or language is invalid
     */
    public static function getDayName(int $dayIndex, string $language = self::LANG_GERMAN): string
    {
        if (!isset(self::$dayNames[$language])) {
            throw new InvalidArgumentException("Unsupported language: {$language}");
        }

        if (!isset(self::$dayNames[$language][$dayIndex])) {
            throw new InvalidArgumentException("Invalid day index: {$dayIndex}");
        }

        return self::$dayNames[$language][$dayIndex];
    }

    /**
     * Get German day name (backward compatibility)
     *
     * @param int $dayIndex Day index (0-7)
     * @return string German day name or "Unbekannt" for invalid index
     */
    public static function getGermanDayName(int $dayIndex): string
    {
        try {
            return self::getDayName($dayIndex, self::LANG_GERMAN);
        } catch (InvalidArgumentException $e) {
            return 'Unbekannt';
        }
    }

    /**
     * Get English day name (backward compatibility)
     *
     * @param int $dayIndex Day index (1-7)
     * @return string English day name
     * @throws InvalidArgumentException If day index is invalid
     */
    public static function getEnglishDayName(int $dayIndex): string
    {
        if ($dayIndex < 1 || $dayIndex > 7) {
            throw new InvalidArgumentException("Invalid day index: {$dayIndex}");
        }

        return self::getDayName($dayIndex, self::LANG_ENGLISH);
    }

    /**
     * Get lowercase English day name for date functions
     *
     * @param int $dayIndex Day index (0-7)
     * @return string Lowercase English day name or "unknown" for invalid index
     */
    public static function getDateDayName(int $dayIndex): string
    {
        return self::$englishDateNames[$dayIndex] ?? 'unknown';
    }

    /**
     * Convert week name to integer
     *
     * @param string $name Week name (German or English abbreviation/full name)
     * @return int Day index (1-7) or -1 if not found
     */
    public static function weekNameToInt(string $name): int
    {
        $name = strtolower(trim($name));
        return self::$weekNameToInt[$name] ?? -1;
    }

    /**
     * Get all supported languages
     *
     * @return array Array of supported language codes
     */
    public static function getSupportedLanguages(): array
    {
        return array_keys(self::$dayNames);
    }

    /**
     * Check if a day index is valid
     *
     * @param int $dayIndex Day index to check
     * @return bool True if valid, false otherwise
     */
    public static function isValidDayIndex(int $dayIndex): bool
    {
        return $dayIndex >= 0 && $dayIndex <= 7;
    }

    /**
     * Normalize day index (convert 7 to 0 for Sunday)
     *
     * @param int $dayIndex Day index (0-7)
     * @return int Normalized day index (0-6)
     */
    public static function normalizeDayIndex(int $dayIndex): int
    {
        return $dayIndex === 7 ? 0 : $dayIndex;
    }
}