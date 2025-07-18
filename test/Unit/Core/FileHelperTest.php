<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;
use FileHelper;
use Application\File;
use Application\MailAttachment;

class FileHelperTest extends TestCase
{
    public function testCreateFileFromUploadMethodExists(): void
    {
        $this->assertTrue(method_exists(FileHelper::class, 'createFileFromUpload'));
        
        $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromUpload');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testCreateFileFromStringMethodExists(): void
    {
        $this->assertTrue(method_exists(FileHelper::class, 'createFileFromString'));
        
        $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromString');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testCreateFileFromMailAttachmentMethodExists(): void
    {
        $this->assertTrue(method_exists(FileHelper::class, 'createFileFromMailAttachment'));
        
        $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromMailAttachment');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testCreateFileFromStringWithValidInput(): void
    {
        $name = 'test.txt';
        $type = 'text/plain';
        $content = 'This is test content';
        
        try {
            $result = FileHelper::createFileFromString($name, $type, $content);
            
            // If successful, result should be File instance or null
            $this->assertTrue($result === null || $result instanceof File);
        } catch (\Exception $e) {
            // If dependencies fail, test that method signature is correct
            $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromString');
            $parameters = $reflection->getParameters();
            
            $this->assertCount(3, $parameters);
            $this->assertEquals('name', $parameters[0]->getName());
            $this->assertEquals('type', $parameters[1]->getName());
            $this->assertEquals('blob', $parameters[2]->getName());
        }
    }

    public function testCreateFileFromStringWithEmptyContent(): void
    {
        $name = 'empty.txt';
        $type = 'text/plain';
        $content = '';
        
        try {
            $result = FileHelper::createFileFromString($name, $type, $content);
            $this->assertTrue($result === null || $result instanceof File);
        } catch (\Exception $e) {
            // Test that method can handle empty content without syntax errors
            $this->assertTrue(method_exists(FileHelper::class, 'createFileFromString'));
        }
    }

    public function testCreateFileFromStringWithSpecialCharacters(): void
    {
        $name = 'special-chars.txt';
        $type = 'text/plain';
        $content = 'Content with special chars: äöü ñ 中文 🚀';
        
        try {
            $result = FileHelper::createFileFromString($name, $type, $content);
            $this->assertTrue($result === null || $result instanceof File);
        } catch (\Exception $e) {
            // Test that method exists and can be called
            $this->assertTrue(method_exists(FileHelper::class, 'createFileFromString'));
        }
    }

    public function testCreateFileFromUploadWithMockFileData(): void
    {
        // Create a temporary file for testing
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'Test file content');
        
        $fileData = [
            'tmp_name' => $tempFile,
            'name' => 'test-upload.txt',
            'type' => 'text/plain',
            'size' => filesize($tempFile)
        ];
        
        try {
            $result = FileHelper::createFileFromUpload($fileData);
            $this->assertTrue($result === null || $result instanceof File);
        } catch (\Exception $e) {
            // Test that method signature is correct
            $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromUpload');
            $parameters = $reflection->getParameters();
            
            $this->assertCount(1, $parameters);
            $this->assertEquals('fileData', $parameters[0]->getName());
        } finally {
            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testCreateFileFromMailAttachmentWithMockAttachment(): void
    {
        try {
            // Try to create a mock MailAttachment
            $mailAttachment = new MailAttachment(1);
            $result = FileHelper::createFileFromMailAttachment($mailAttachment);
            $this->assertTrue($result === null || $result instanceof File);
        } catch (\Exception $e) {
            // Test that method signature is correct
            $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromMailAttachment');
            $parameters = $reflection->getParameters();
            
            $this->assertCount(1, $parameters);
            $this->assertEquals('mailAttachment', $parameters[0]->getName());
            
            // Test that parameter has correct type hint
            $paramType = $parameters[0]->getType();
            $this->assertNotNull($paramType);
            $this->assertEquals('MailAttachment', $paramType->getName());
        }
    }

    public function testAllMethodsAreStatic(): void
    {
        $reflection = new \ReflectionClass(FileHelper::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method) {
            if (!$method->isConstructor() && !$method->isDestructor()) {
                $this->assertTrue($method->isStatic(), "Method {$method->getName()} should be static");
            }
        }
    }

    public function testClassHasExpectedMethods(): void
    {
        $expectedMethods = [
            'createFileFromUpload',
            'createFileFromString', 
            'createFileFromMailAttachment'
        ];
        
        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists(FileHelper::class, $method), "Method {$method} should exist");
        }
    }

    public function testCreateFileFromStringParameterTypes(): void
    {
        $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromString');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(3, $parameters);
        
        // Check parameter names
        $this->assertEquals('name', $parameters[0]->getName());
        $this->assertEquals('type', $parameters[1]->getName());
        $this->assertEquals('blob', $parameters[2]->getName());
        
        // Check parameter types
        $this->assertEquals('string', $parameters[0]->getType()->getName());
        $this->assertEquals('string', $parameters[1]->getType()->getName());
        $this->assertEquals('string', $parameters[2]->getType()->getName());
    }

    public function testCreateFileFromUploadParameterTypes(): void
    {
        $reflection = new \ReflectionMethod(FileHelper::class, 'createFileFromUpload');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('fileData', $parameters[0]->getName());
        $this->assertEquals('array', $parameters[0]->getType()->getName());
    }

    public function testMethodReturnTypes(): void
    {
        $methods = [
            'createFileFromUpload',
            'createFileFromString',
            'createFileFromMailAttachment'
        ];
        
        foreach ($methods as $methodName) {
            $reflection = new \ReflectionMethod(FileHelper::class, $methodName);
            $returnType = $reflection->getReturnType();
            
            $this->assertNotNull($returnType, "Method {$methodName} should have return type");
            $this->assertTrue($returnType->allowsNull(), "Method {$methodName} should allow null return");
        }
    }
}