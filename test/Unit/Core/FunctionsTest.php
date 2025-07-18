<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for utility functions in functions.php
 */
class FunctionsTest extends TestCase
{
    /**
     * Test is_decimal function with decimal numbers
     */
    public function testIsDecimalWithDecimalNumberReturnsTrue(): void
    {
        $this->assertTrue(is_decimal(3.14));
        $this->assertTrue(is_decimal(0.5));
        $this->assertTrue(is_decimal(-2.7));
    }

    /**
     * Test is_decimal function with integer numbers
     */
    public function testIsDecimalWithIntegerNumberReturnsFalse(): void
    {
        $this->assertFalse(is_decimal(5));
        $this->assertFalse(is_decimal(0));
        $this->assertFalse(is_decimal(-10));
    }

    /**
     * Test is_decimal function with non-numeric values
     */
    public function testIsDecimalWithNonNumericValueReturnsFalse(): void
    {
        $this->assertFalse(is_decimal('string'));
        $this->assertFalse(is_decimal(null));
        $this->assertFalse(is_decimal(true));
        $this->assertFalse(is_decimal([]));
    }

    /**
     * Test formatNumber function with decimal numbers
     */
    public function testFormatNumberWithDecimalNumber(): void
    {
        $result = formatNumber(1234.56, 2);
        $this->assertEquals('1.234,56', $result);
    }

    /**
     * Test formatNumber function with integer numbers
     */
    public function testFormatNumberWithIntegerNumber(): void
    {
        $result = formatNumber(1234.0, 2);
        $this->assertEquals('1.234', $result);
    }

    /**
     * Test formatNumber function with zero decimals
     */
    public function testFormatNumberWithZeroDecimals(): void
    {
        $result = formatNumber(1234.56, 0);
        $this->assertEquals('1.235', $result); // Should round
    }

    /**
     * Test getClientIP function with X-Forwarded-For header (single IP)
     */
    public function testGetClientIPWithXForwardedForSingleIP(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.1';
        
        $result = getClientIP();
        
        $this->assertEquals('192.168.1.1', $result);
        
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    /**
     * Test getClientIP function with X-Forwarded-For header (multiple IPs)
     */
    public function testGetClientIPWithXForwardedForMultipleIPs(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.1, 10.0.0.1, 172.16.0.1';
        
        $result = getClientIP();
        
        $this->assertEquals('192.168.1.1', $result);
        
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    /**
     * Test getClientIP function with REMOTE_ADDR
     */
    public function testGetClientIPWithRemoteAddr(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
        
        $result = getClientIP();
        
        $this->assertEquals('192.168.1.100', $result);
        
        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Test getClientIP function with no IP headers
     */
    public function testGetClientIPWithNoHeaders(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['REMOTE_ADDR']);
        
        $result = getClientIP();
        
        $this->assertEquals('', $result);
    }

    /**
     * Test slugify function with normal text
     */
    public function testSlugifyWithNormalText(): void
    {
        $result = slugify('Hello World');
        $this->assertEquals('hello-world', $result);
    }

    /**
     * Test slugify function with German umlauts
     */
    public function testSlugifyWithGermanUmlauts(): void
    {
        $result = slugify('Müller & Söhne');
        $this->assertEquals('mueller-soehne', $result);
    }

    /**
     * Test slugify function with special characters
     */
    public function testSlugifyWithSpecialCharacters(): void
    {
        $result = slugify('Hello@World#123!');
        $this->assertEquals('hello-world-123', $result);
    }

    /**
     * Test slugify function with empty string
     */
    public function testSlugifyWithEmptyString(): void
    {
        $result = slugify('');
        $this->assertEquals('n-a', $result);
    }

    /**
     * Test file_extension function with normal filename
     */
    public function testFileExtensionWithNormalFilename(): void
    {
        $result = file_extension('document.pdf');
        
        $this->assertEquals('document', $result['filename']);
        $this->assertEquals('.pdf', $result['extension']);
        $this->assertEquals('pdf', $result['extension_undotted']);
    }

    /**
     * Test file_extension function with multiple dots
     */
    public function testFileExtensionWithMultipleDots(): void
    {
        $result = file_extension('archive.tar.gz');
        
        $this->assertEquals('archive.tar', $result['filename']);
        $this->assertEquals('.gz', $result['extension']);
        $this->assertEquals('gz', $result['extension_undotted']);
    }

    /**
     * Test weekDayNameGerman function with valid days
     */
    public function testWeekDayNameGermanWithValidDays(): void
    {
        $this->assertEquals('Montag', weekDayNameGerman(1));
        $this->assertEquals('Dienstag', weekDayNameGerman(2));
        $this->assertEquals('Mittwoch', weekDayNameGerman(3));
        $this->assertEquals('Donnerstag', weekDayNameGerman(4));
        $this->assertEquals('Freitag', weekDayNameGerman(5));
        $this->assertEquals('Samstag', weekDayNameGerman(6));
        $this->assertEquals('Sonntag', weekDayNameGerman(7));
        $this->assertEquals('Sonntag', weekDayNameGerman(0)); // Sunday can be 0 or 7
    }

    /**
     * Test weekDayNameGerman function with invalid day
     */
    public function testWeekDayNameGermanWithInvalidDay(): void
    {
        $this->assertEquals('Unbekannt', weekDayNameGerman(8));
        $this->assertEquals('Unbekannt', weekDayNameGerman(-1));
    }

    /**
     * Test castToInt function
     */
    public function testCastToInt(): void
    {
        $this->assertEquals(5, castToInt('5'));
        $this->assertEquals(0, castToInt('0'));
        $this->assertEquals(3, castToInt(3.14));
        $this->assertEquals(1, castToInt(true));
        $this->assertEquals(0, castToInt(false));
    }

    /**
     * Test isCliInstance function
     */
    public function testIsCliInstance(): void
    {
        // In PHPUnit, we're typically not in CLI mode, but this depends on how tests are run
        $result = isCliInstance();
        $this->assertIsBool($result);
    }

    /**
     * Test startsWith function
     */
    public function testStartsWith(): void
    {
        $this->assertTrue(startsWith('Hello World', 'Hello'));
        $this->assertTrue(startsWith('Hello World', ''));
        $this->assertFalse(startsWith('Hello World', 'World'));
        $this->assertFalse(startsWith('Hello World', 'hello')); // Case sensitive
    }

    /**
     * Test endsWith function
     */
    public function testEndsWith(): void
    {
        $this->assertTrue(endsWith('Hello World', 'World'));
        $this->assertTrue(endsWith('Hello World', ''));
        $this->assertFalse(endsWith('Hello World', 'Hello'));
        $this->assertFalse(endsWith('Hello World', 'world')); // Case sensitive
    }

    /**
     * Test guid function returns valid GUID format
     */
    public function testGuidReturnsValidFormat(): void
    {
        $guid = guid();
        
        $this->assertIsString($guid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $guid);
    }

    /**
     * Test guid function returns unique values
     */
    public function testGuidReturnsUniqueValues(): void
    {
        $guid1 = guid();
        $guid2 = guid();
        
        $this->assertNotEquals($guid1, $guid2);
    }

    /**
     * Test calcPercentageProgress function
     */
    public function testCalcPercentageProgress(): void
    {
        $this->assertEquals(0.5, calcPercentageProgress(5, 0, 10));
        $this->assertEquals(0.0, calcPercentageProgress(0, 0, 10));
        $this->assertEquals(1.0, calcPercentageProgress(10, 0, 10));
        $this->assertEquals(1.0, calcPercentageProgress(5, 5, 5)); // Edge case: min == max
    }

    /**
     * Test inRange function
     */
    public function testInRange(): void
    {
        $this->assertTrue(inRange(5, 0, 10));
        $this->assertFalse(inRange(0, 0, 10)); // Exclusive range
        $this->assertFalse(inRange(10, 0, 10)); // Exclusive range
        $this->assertFalse(inRange(-1, 0, 10));
        $this->assertFalse(inRange(11, 0, 10));
    }

    /**
     * Test br2nl function
     */
    public function testBr2nl(): void
    {
        $this->assertEquals("Hello\nWorld", br2nl('Hello<br>World'));
        $this->assertEquals("Hello\nWorld", br2nl('Hello<br/>World'));
        $this->assertEquals("Hello\nWorld", br2nl('Hello<br />World'));
        $this->assertEquals("Hello\n\nWorld", br2nl('Hello<BR><br/>World'));
    }

    /**
     * Test clean_input function
     */
    public function testCleanInput(): void
    {
        $this->assertEquals('Hello World', clean_input('  Hello World  '));
        $this->assertEquals('Hello World', clean_input('<script>Hello World</script>'));
        $this->assertEquals('&lt;test&gt;', clean_input('<test>'));
    }

    /**
     * Test totalHours function
     */
    public function testTotalHours(): void
    {
        $this->assertEquals('1h 00m', totalHours(3600)); // 1 hour
        $this->assertEquals('2h 30m', totalHours(9000)); // 2.5 hours
        $this->assertEquals('0h 30m', totalHours(1800)); // 30 minutes
    }

    /**
     * Test hours function
     */
    public function testHours(): void
    {
        $this->assertEquals(3600, hours(1)); // 1 hour = 3600 seconds
        $this->assertEquals(7200, hours(2)); // 2 hours = 7200 seconds
    }

    /**
     * Test days function
     */
    public function testDays(): void
    {
        $this->assertEquals(86400, days(1)); // 1 day = 86400 seconds
        $this->assertEquals(172800, days(2)); // 2 days = 172800 seconds
    }
}