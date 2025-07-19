<?php

namespace Core;

/**
 * Input validation and sanitization utility class
 * Provides secure methods for handling user input from $_GET, $_POST, etc.
 */
class InputValidator
{
    /**
     * Validates and sanitizes GET parameter
     *
     * @param string $key Parameter name
     * @param int $filter Filter type (FILTER_SANITIZE_STRING, FILTER_VALIDATE_INT, etc.)
     * @param mixed $default Default value if parameter is missing or invalid
     * @param array $options Additional filter options
     * @return mixed Sanitized value or default
     */
    public static function getParam(string $key, int $filter = FILTER_SANITIZE_SPECIAL_CHARS, mixed $default = null, array $options = []): mixed
    {
        $value = filter_input(INPUT_GET, $key, $filter, $options);
        return ($value !== false && $value !== null) ? $value : $default;
    }

    /**
     * Validates and sanitizes POST parameter
     *
     * @param string $key Parameter name
     * @param int $filter Filter type
     * @param mixed $default Default value if parameter is missing or invalid
     * @param array $options Additional filter options
     * @return mixed Sanitized value or default
     */
    public static function postParam(string $key, int $filter = FILTER_SANITIZE_SPECIAL_CHARS, mixed $default = null, array $options = []): mixed
    {
        $value = filter_input(INPUT_POST, $key, $filter, $options);
        return ($value !== false && $value !== null) ? $value : $default;
    }

    /**
     * Validates required POST parameters
     *
     * @param array $requiredParams Array of required parameter names
     * @return array Array of validation errors (empty if all valid)
     */
    public static function validateRequiredPost(array $requiredParams): array
    {
        $errors = [];
        foreach ($requiredParams as $param) {
            if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
                $errors[] = "Missing required parameter: {$param}";
            }
        }
        return $errors;
    }

    /**
     * Validates required GET parameters
     *
     * @param array $requiredParams Array of required parameter names
     * @return array Array of validation errors (empty if all valid)
     */
    public static function validateRequiredGet(array $requiredParams): array
    {
        $errors = [];
        foreach ($requiredParams as $param) {
            if (!isset($_GET[$param]) || empty(trim($_GET[$param]))) {
                $errors[] = "Missing required parameter: {$param}";
            }
        }
        return $errors;
    }

    /**
     * Safely creates DateTime object with error handling
     *
     * @param string $dateString Date string to parse
     * @param string $format Expected format (optional)
     * @return DateTime|null DateTime object or null if invalid
     */
    public static function createDateTime(string $dateString, string $format = null): ?\DateTime
    {
        try {
            if ($format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                return $date !== false ? $date : null;
            }
            return new \DateTime($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validates and sanitizes integer parameter
     *
     * @param string $value Value to validate
     * @param int $min Minimum allowed value
     * @param int $max Maximum allowed value
     * @return int|null Valid integer or null if invalid
     */
    public static function validateInt(string $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): ?int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);
        return $int !== false ? $int : null;
    }

    /**
     * Validates email address
     *
     * @param string $email Email to validate
     * @return string|null Valid email or null if invalid
     */
    public static function validateEmail(string $email): ?string
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $email !== false ? $email : null;
    }

    /**
     * Validates URL
     *
     * @param string $url URL to validate
     * @return string|null Valid URL or null if invalid
     */
    public static function validateUrl(string $url): ?string
    {
        $url = filter_var($url, FILTER_VALIDATE_URL);
        return $url !== false ? $url : null;
    }

    /**
     * Sanitizes string for HTML output (prevents XSS)
     *
     * @param string $string String to sanitize
     * @return string Sanitized string
     */
    public static function sanitizeHtml(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validates GUID format
     *
     * @param string $guid GUID to validate
     * @return string|null Valid GUID or null if invalid
     */
    public static function validateGuid(string $guid): ?string
    {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $guid)) {
            return $guid;
        }
        return null;
    }
}