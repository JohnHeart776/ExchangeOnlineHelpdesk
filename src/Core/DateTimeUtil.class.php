<?php

/**
 * Unified DateTime Utility Class
 * Consolidates functionality from DateHelper and DateTimeHelper
 * Eliminates duplication between multiple date/time utility classes
 */
class DateTimeUtil
{
    /**
     * Create DateTime object from string or current time
     * Consolidates DateHelper::getDate() and DateTimeHelper::getNow()
     * 
     * @param string|null $dtString
     * @return DateTime
     * @throws DateMalformedStringException
     */
    public static function getDateTime(?string $dtString = null): DateTime
    {
        if ($dtString) {
            return new DateTime($dtString);
        }
        return new DateTime();
    }

    /**
     * Get current DateTime (alias for getDateTime with no parameters)
     * Replaces DateTimeHelper::getNow()
     * 
     * @return DateTime
     */
    public static function getNow(): DateTime
    {
        return new DateTime();
    }

    /**
     * Format DateTime for MySQL storage
     * From DateHelper::getDateTimeForMysql()
     * 
     * @param string|null $dtString
     * @return string
     * @throws DateMalformedStringException
     */
    public static function getDateTimeForMysql(?string $dtString = null): string
    {
        return self::getDateTime($dtString)->format("Y-m-d H:i:s");
    }

    /**
     * Get weekday name in German
     * From DateTimeHelper::getWeekdayInGerman()
     * 
     * @param DateTime $date
     * @return string
     */
    public static function getWeekdayInGerman(DateTime $date): string
    {
        $weekdays = [
            1 => 'Montag',
            2 => 'Dienstag',
            3 => 'Mittwoch',
            4 => 'Donnerstag',
            5 => 'Freitag',
            6 => 'Samstag',
            7 => 'Sonntag',
        ];

        return $weekdays[(int)$date->format('N')];
    }

    /**
     * Get weekday name in English
     * Additional utility method
     * 
     * @param DateTime $date
     * @return string
     */
    public static function getWeekdayInEnglish(DateTime $date): string
    {
        return $date->format('l');
    }

    /**
     * Format DateTime for display
     * Common formatting utility
     * 
     * @param DateTime $date
     * @param string $format
     * @return string
     */
    public static function formatForDisplay(DateTime $date, string $format = 'Y-m-d H:i:s'): string
    {
        return $date->format($format);
    }

    /**
     * Check if date is today
     * Common utility method
     * 
     * @param DateTime $date
     * @return bool
     */
    public static function isToday(DateTime $date): bool
    {
        return $date->format('Y-m-d') === (new DateTime())->format('Y-m-d');
    }

    /**
     * Get time difference in human readable format
     * Common utility method
     * 
     * @param DateTime $from
     * @param DateTime|null $to
     * @return string
     */
    public static function getTimeDifference(DateTime $from, ?DateTime $to = null): string
    {
        if (!$to) {
            $to = new DateTime();
        }
        
        $interval = $from->diff($to);
        
        if ($interval->days > 0) {
            return $interval->days . ' day' . ($interval->days > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'just now';
        }
    }
}