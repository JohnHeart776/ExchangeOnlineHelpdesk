<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;
use DateHelper;
use DateTime;
use DateMalformedStringException;

/**
 * Unit tests for DateHelper class
 */
class DateHelperTest extends TestCase
{
    public function testGetDateWithoutParameterReturnsCurrentDateTime(): void
    {
        $result = DateHelper::getDate();
        
        $this->assertInstanceOf(DateTime::class, $result);
        
        // Check that the returned date is close to current time (within 1 second)
        $now = new DateTime();
        $diff = $now->getTimestamp() - $result->getTimestamp();
        $this->assertLessThanOrEqual(1, abs($diff));
    }

    public function testGetDateWithNullParameterReturnsCurrentDateTime(): void
    {
        $result = DateHelper::getDate(null);
        
        $this->assertInstanceOf(DateTime::class, $result);
        
        // Check that the returned date is close to current time (within 1 second)
        $now = new DateTime();
        $diff = $now->getTimestamp() - $result->getTimestamp();
        $this->assertLessThanOrEqual(1, abs($diff));
    }

    public function testGetDateWithValidDateStringReturnsCorrectDateTime(): void
    {
        $dateString = '2023-12-25 15:30:45';
        $result = DateHelper::getDate($dateString);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-12-25 15:30:45', $result->format('Y-m-d H:i:s'));
    }

    public function testGetDateWithValidDateStringDifferentFormat(): void
    {
        $dateString = '25-12-2023';
        $result = DateHelper::getDate($dateString);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-12-25', $result->format('Y-m-d'));
    }

    public function testGetDateWithIsoDateString(): void
    {
        $dateString = '2023-12-25T15:30:45Z';
        $result = DateHelper::getDate($dateString);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-12-25', $result->format('Y-m-d'));
        $this->assertEquals('15:30:45', $result->format('H:i:s'));
    }

    public function testGetDateWithInvalidDateStringThrowsException(): void
    {
        $this->expectException(DateMalformedStringException::class);
        
        DateHelper::getDate('invalid-date-string');
    }

    public function testGetDateWithEmptyStringThrowsException(): void
    {
        $this->expectException(DateMalformedStringException::class);
        
        DateHelper::getDate('');
    }

    public function testGetDateTimeForMysqlWithoutParameterReturnsCurrentDateTimeInMysqlFormat(): void
    {
        $result = DateHelper::getDateTimeForMysql();
        
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $result);
        
        // Verify it's a valid date by parsing it back
        $parsedDate = DateTime::createFromFormat('Y-m-d H:i:s', $result);
        $this->assertInstanceOf(DateTime::class, $parsedDate);
        
        // Check that the returned date is close to current time (within 1 second)
        $now = new DateTime();
        $diff = $now->getTimestamp() - $parsedDate->getTimestamp();
        $this->assertLessThanOrEqual(1, abs($diff));
    }

    public function testGetDateTimeForMysqlWithNullParameterReturnsCurrentDateTimeInMysqlFormat(): void
    {
        $result = DateHelper::getDateTimeForMysql(null);
        
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $result);
        
        // Verify it's a valid date by parsing it back
        $parsedDate = DateTime::createFromFormat('Y-m-d H:i:s', $result);
        $this->assertInstanceOf(DateTime::class, $parsedDate);
    }

    public function testGetDateTimeForMysqlWithValidDateStringReturnsCorrectFormat(): void
    {
        $dateString = '2023-12-25 15:30:45';
        $result = DateHelper::getDateTimeForMysql($dateString);
        
        $this->assertEquals('2023-12-25 15:30:45', $result);
    }

    public function testGetDateTimeForMysqlWithDifferentDateFormatReturnsCorrectFormat(): void
    {
        $dateString = '25-12-2023 15:30:45';
        $result = DateHelper::getDateTimeForMysql($dateString);
        
        $this->assertEquals('2023-12-25 15:30:45', $result);
    }

    public function testGetDateTimeForMysqlWithIsoDateStringReturnsCorrectFormat(): void
    {
        $dateString = '2023-12-25T15:30:45Z';
        $result = DateHelper::getDateTimeForMysql($dateString);
        
        $this->assertStringStartsWith('2023-12-25 15:30:45', $result);
    }

    public function testGetDateTimeForMysqlWithInvalidDateStringThrowsException(): void
    {
        $this->expectException(DateMalformedStringException::class);
        
        DateHelper::getDateTimeForMysql('invalid-date-string');
    }

    public function testGetDateTimeForMysqlWithEmptyStringThrowsException(): void
    {
        $this->expectException(DateMalformedStringException::class);
        
        DateHelper::getDateTimeForMysql('');
    }

    public function testGetDateTimeForMysqlFormatIsConsistent(): void
    {
        $dateString = '2023-01-01 00:00:00';
        $result = DateHelper::getDateTimeForMysql($dateString);
        
        // Test that the format is exactly what MySQL expects
        $this->assertEquals('2023-01-01 00:00:00', $result);
        $this->assertEquals(19, strlen($result)); // YYYY-MM-DD HH:MM:SS = 19 characters
    }

    public function testBothMethodsReturnConsistentResults(): void
    {
        $dateString = '2023-12-25 15:30:45';
        
        $dateObject = DateHelper::getDate($dateString);
        $mysqlString = DateHelper::getDateTimeForMysql($dateString);
        
        $this->assertEquals($mysqlString, $dateObject->format('Y-m-d H:i:s'));
    }
}