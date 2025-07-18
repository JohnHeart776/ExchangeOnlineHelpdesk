<?php

namespace Test\Unit\Auth;

use PHPUnit\Framework\TestCase;
use Auth\GraphCertificateAuthenticator;
use Auth\BaseAuthenticator;

class GraphCertificateAuthenticatorTest extends TestCase
{
    private $mockTenantId = 'test-tenant-id';
    private $mockClientId = 'test-client-id';
    private $mockCert = 'test-certificate';
    private $mockKey = 'test-private-key';
    private $mockKeyPass = 'test-key-password';

    public function testConstructorCreatesInstance(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            $this->mockKeyPass
        );

        $this->assertInstanceOf(GraphCertificateAuthenticator::class, $authenticator);
        $this->assertInstanceOf(BaseAuthenticator::class, $authenticator);
    }

    public function testConstructorWithNullKeyPassword(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            null
        );

        $this->assertInstanceOf(GraphCertificateAuthenticator::class, $authenticator);
    }

    public function testBase64UrlEncodeReturnsString(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            $this->mockKeyPass
        );

        // Use reflection to test private method
        $reflection = new \ReflectionClass($authenticator);
        $method = $reflection->getMethod('base64UrlEncode');
        $method->setAccessible(true);

        $testData = 'test data';
        $result = $method->invoke($authenticator, $testData);

        $this->assertIsString($result);
        $this->assertStringNotContainsString('+', $result);
        $this->assertStringNotContainsString('/', $result);
        $this->assertStringNotContainsString('=', $result);
    }

    public function testBase64UrlEncodeWithEmptyString(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            $this->mockKeyPass
        );

        // Use reflection to test private method
        $reflection = new \ReflectionClass($authenticator);
        $method = $reflection->getMethod('base64UrlEncode');
        $method->setAccessible(true);

        $result = $method->invoke($authenticator, '');

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testBase64UrlEncodeWithSpecialCharacters(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            $this->mockKeyPass
        );

        // Use reflection to test private method
        $reflection = new \ReflectionClass($authenticator);
        $method = $reflection->getMethod('base64UrlEncode');
        $method->setAccessible(true);

        $testData = 'test+data/with=special';
        $result = $method->invoke($authenticator, $testData);

        $this->assertIsString($result);
        // Result should not contain URL-unsafe characters
        $this->assertStringNotContainsString('+', $result);
        $this->assertStringNotContainsString('/', $result);
        $this->assertStringNotContainsString('=', $result);
    }

    public function testLoadKeysMethodExists(): void
    {
        $this->assertTrue(method_exists(GraphCertificateAuthenticator::class, 'loadKeys'));
        
        $reflection = new \ReflectionMethod(GraphCertificateAuthenticator::class, 'loadKeys');
        $this->assertTrue($reflection->isPrivate());
    }

    public function testGetAccessTokenMethodExists(): void
    {
        $this->assertTrue(method_exists(GraphCertificateAuthenticator::class, 'getAccessToken'));
        
        $reflection = new \ReflectionMethod(GraphCertificateAuthenticator::class, 'getAccessToken');
        $this->assertTrue($reflection->isPublic());
    }

    public function testCreateJwtAssertionMethodExists(): void
    {
        $this->assertTrue(method_exists(GraphCertificateAuthenticator::class, 'createJwtAssertion'));
        
        $reflection = new \ReflectionMethod(GraphCertificateAuthenticator::class, 'createJwtAssertion');
        $this->assertTrue($reflection->isPrivate());
    }

    public function testGetOrganizationInfoMethodExists(): void
    {
        $this->assertTrue(method_exists(GraphCertificateAuthenticator::class, 'getOrganizationInfo'));
        
        $reflection = new \ReflectionMethod(GraphCertificateAuthenticator::class, 'getOrganizationInfo');
        $this->assertTrue($reflection->isPublic());
    }

    public function testGetAccessTokenThrowsExceptionWithInvalidCredentials(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            'invalid-tenant',
            'invalid-client',
            'invalid-cert',
            'invalid-key',
            null
        );

        $this->expectException(\Exception::class);
        $authenticator->getAccessToken();
    }

    public function testGetOrganizationInfoThrowsExceptionWithInvalidCredentials(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            'invalid-tenant',
            'invalid-client',
            'invalid-cert',
            'invalid-key',
            null
        );

        $this->expectException(\Exception::class);
        $authenticator->getOrganizationInfo();
    }

    public function testClassExtendsBaseAuthenticator(): void
    {
        $reflection = new \ReflectionClass(GraphCertificateAuthenticator::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertNotFalse($parentClass);
        $this->assertEquals('Auth\BaseAuthenticator', $parentClass->getName());
    }

    public function testClassImplementsAbstractMethod(): void
    {
        $reflection = new \ReflectionClass(GraphCertificateAuthenticator::class);
        $method = $reflection->getMethod('getAccessToken');
        
        $this->assertTrue($method->isPublic());
        $this->assertFalse($method->isAbstract());
    }

    public function testConstructorSetsProperties(): void
    {
        $authenticator = new GraphCertificateAuthenticator(
            $this->mockTenantId,
            $this->mockClientId,
            $this->mockCert,
            $this->mockKey,
            $this->mockKeyPass
        );

        // Test that constructor doesn't throw exception
        $this->assertInstanceOf(GraphCertificateAuthenticator::class, $authenticator);
        
        // We can't directly test private properties, but we can test that the object was created successfully
        $reflection = new \ReflectionClass($authenticator);
        $this->assertTrue($reflection->hasProperty('tenantId'));
        $this->assertTrue($reflection->hasProperty('clientId'));
        $this->assertTrue($reflection->hasProperty('cert'));
        $this->assertTrue($reflection->hasProperty('key'));
        $this->assertTrue($reflection->hasProperty('keyPass'));
    }
}