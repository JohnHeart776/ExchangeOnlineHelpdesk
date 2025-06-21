<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;
use GuidHelper;

/**
 * Unit tests for GuidHelper class
 */
class GuidHelperTest extends TestCase
{
    public function testGenerateGuidReturnsString(): void
    {
        $result = GuidHelper::generateGuid();
        
        $this->assertIsString($result);
    }

    public function testGenerateGuidReturnsCorrectFormat(): void
    {
        $result = GuidHelper::generateGuid();
        
        // GUID format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        // Total length should be 36 characters (32 hex + 4 hyphens)
        $this->assertEquals(36, strlen($result));
        
        // Test the pattern: 8-4-4-4-12 hex digits separated by hyphens
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        $this->assertMatchesRegularExpression($pattern, $result);
    }

    public function testGenerateGuidReturnsValidUuidV4Format(): void
    {
        $result = GuidHelper::generateGuid();
        
        // Split the GUID into parts
        $parts = explode('-', $result);
        
        $this->assertCount(5, $parts);
        $this->assertEquals(8, strlen($parts[0])); // First part: 8 hex digits
        $this->assertEquals(4, strlen($parts[1])); // Second part: 4 hex digits
        $this->assertEquals(4, strlen($parts[2])); // Third part: 4 hex digits
        $this->assertEquals(4, strlen($parts[3])); // Fourth part: 4 hex digits
        $this->assertEquals(12, strlen($parts[4])); // Fifth part: 12 hex digits
        
        // Check UUID v4 specific requirements
        // The third part should start with '4' (version 4)
        $this->assertEquals('4', $parts[2][0]);
        
        // The fourth part should start with '8', '9', 'a', or 'b' (variant bits)
        $firstChar = strtolower($parts[3][0]);
        $this->assertContains($firstChar, ['8', '9', 'a', 'b']);
    }

    public function testGenerateGuidReturnsUniqueValues(): void
    {
        $guids = [];
        $iterations = 100;
        
        // Generate multiple GUIDs and check for uniqueness
        for ($i = 0; $i < $iterations; $i++) {
            $guid = GuidHelper::generateGuid();
            $this->assertNotContains($guid, $guids, "Duplicate GUID generated: {$guid}");
            $guids[] = $guid;
        }
        
        $this->assertCount($iterations, array_unique($guids));
    }

    public function testGenerateGuidConsistentFormat(): void
    {
        // Generate multiple GUIDs and verify they all follow the same format
        for ($i = 0; $i < 10; $i++) {
            $guid = GuidHelper::generateGuid();
            
            // Check that all characters except hyphens are hexadecimal
            $withoutHyphens = str_replace('-', '', $guid);
            $this->assertEquals(32, strlen($withoutHyphens));
            $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/i', $withoutHyphens);
            
            // Check hyphen positions
            $this->assertEquals('-', $guid[8]);
            $this->assertEquals('-', $guid[13]);
            $this->assertEquals('-', $guid[18]);
            $this->assertEquals('-', $guid[23]);
        }
    }

    public function testGenerateGuidIsLowercase(): void
    {
        $result = GuidHelper::generateGuid();
        
        // Remove hyphens and check if all letters are lowercase
        $withoutHyphens = str_replace('-', '', $result);
        $this->assertEquals(strtolower($withoutHyphens), $withoutHyphens);
    }

    public function testGenerateGuidMultipleCallsReturnDifferentValues(): void
    {
        $guid1 = GuidHelper::generateGuid();
        $guid2 = GuidHelper::generateGuid();
        $guid3 = GuidHelper::generateGuid();
        
        $this->assertNotEquals($guid1, $guid2);
        $this->assertNotEquals($guid2, $guid3);
        $this->assertNotEquals($guid1, $guid3);
    }

    public function testGenerateGuidVersionAndVariantBits(): void
    {
        // Test multiple GUIDs to ensure version and variant bits are correct
        for ($i = 0; $i < 20; $i++) {
            $guid = GuidHelper::generateGuid();
            $parts = explode('-', $guid);
            
            // Version should be 4 (UUID v4)
            $version = hexdec($parts[2][0]);
            $this->assertEquals(4, $version, "GUID version should be 4, got: {$version} in GUID: {$guid}");
            
            // Variant should be 10xx (binary), which means first hex digit should be 8, 9, A, or B
            $variantHex = $parts[3][0];
            $variantDec = hexdec($variantHex);
            $this->assertGreaterThanOrEqual(8, $variantDec, "Variant should be >= 8, got: {$variantDec} in GUID: {$guid}");
            $this->assertLessThanOrEqual(11, $variantDec, "Variant should be <= 11, got: {$variantDec} in GUID: {$guid}");
        }
    }

    public function testGenerateGuidStaticMethodExists(): void
    {
        $this->assertTrue(method_exists(GuidHelper::class, 'generateGuid'));
        
        $reflection = new \ReflectionMethod(GuidHelper::class, 'generateGuid');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    public function testGenerateGuidPerformance(): void
    {
        $startTime = microtime(true);
        
        // Generate 1000 GUIDs to test performance
        for ($i = 0; $i < 1000; $i++) {
            GuidHelper::generateGuid();
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should be able to generate 1000 GUIDs in less than 1 second
        $this->assertLessThan(1.0, $executionTime, "GUID generation is too slow: {$executionTime} seconds for 1000 GUIDs");
    }
}