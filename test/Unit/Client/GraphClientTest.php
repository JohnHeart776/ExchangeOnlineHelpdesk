<?php

namespace Test\Unit\Client;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Client\GraphClient;
use Auth\GraphCertificateAuthenticator;
use Exception;

/**
 * Unit tests for GraphClient class
 * 
 * Note: Since GraphClient has private methods for HTTP requests,
 * these tests focus on the public interface and constructor behavior.
 * Integration tests would be needed to test the actual API calls.
 */
class GraphClientTest extends TestCase
{
    private MockObject $mockAuthenticator;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock authenticator
        $this->mockAuthenticator = $this->createMock(GraphCertificateAuthenticator::class);
        $this->mockAuthenticator->method('getAccessToken')
                                ->willReturn('mock_access_token_12345');
    }

    public function testConstructorAcceptsAuthenticator(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertInstanceOf(GraphClient::class, $client);
    }

    public function testConstructorRequiresAuthenticator(): void
    {
        // Test that constructor requires an authenticator parameter
        $this->expectException(\TypeError::class);

        // This should fail because constructor requires GraphCertificateAuthenticator
        new GraphClient();
    }

    public function testGetUserInfoMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'getUserInfo'));
    }

    public function testGetUserImageMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'getUserImage'));
    }

    public function testSendMailAsUserMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'sendMailAsUser'));
    }

    public function testSendMultipartMailAsUserMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'sendMultipartMailAsUser'));
    }

    public function testFetchMailsMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'fetchMails'));
    }

    public function testFetchAttachmentsMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'fetchAttachments'));
    }

    public function testGetMailFromAzureAsJsonObjectMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'getMailFromAzureAsJsonObject'));
    }

    public function testGetMailFromAzureAsGraphMailMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'getMailFromAzureAsGraphMail'));
    }

    public function testPrefixMailSubjectMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'prefixMailSubject'));
    }

    public function testSuffixMailSubjectMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'suffixMailSubject'));
    }

    public function testUpdateMailSubjectMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'updateMailSubject'));
    }

    public function testFetchUserPageMethodExists(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        $this->assertTrue(method_exists($client, 'fetchUserPage'));
    }

    /**
     * Test that the class has the expected private properties
     * by checking if they're defined in the class
     */
    public function testClassHasExpectedProperties(): void
    {
        $reflection = new \ReflectionClass(GraphClient::class);

        $this->assertTrue($reflection->hasProperty('graphEndpoint'));
        $this->assertTrue($reflection->hasProperty('auth'));

        $graphEndpointProperty = $reflection->getProperty('graphEndpoint');
        $this->assertTrue($graphEndpointProperty->isPrivate());

        $authProperty = $reflection->getProperty('auth');
        $this->assertTrue($authProperty->isPrivate());
    }

    /**
     * Test that the class has the expected private methods
     */
    public function testClassHasExpectedPrivateMethods(): void
    {
        $reflection = new \ReflectionClass(GraphClient::class);

        $this->assertTrue($reflection->hasMethod('getAccessToken'));
        $this->assertTrue($reflection->hasMethod('getRequest'));
        $this->assertTrue($reflection->hasMethod('postJsonRequest'));
        $this->assertTrue($reflection->hasMethod('patchJsonRequest'));

        $getAccessTokenMethod = $reflection->getMethod('getAccessToken');
        $this->assertTrue($getAccessTokenMethod->isPrivate());

        $getRequestMethod = $reflection->getMethod('getRequest');
        $this->assertTrue($getRequestMethod->isPrivate());

        $postJsonRequestMethod = $reflection->getMethod('postJsonRequest');
        $this->assertTrue($postJsonRequestMethod->isPrivate());

        $patchJsonRequestMethod = $reflection->getMethod('patchJsonRequest');
        $this->assertTrue($patchJsonRequestMethod->isPrivate());
    }

    /**
     * Note: The following tests would require integration testing or mocking of CurlHelper
     * since the GraphClient uses private methods for HTTP requests.
     * These tests verify the public interface exists and can be called.
     */

    public function testPublicMethodsAcceptCorrectParameters(): void
    {
        $client = new GraphClient($this->mockAuthenticator);

        // Test that methods exist and accept the expected parameter types
        $reflection = new \ReflectionClass($client);

        // Test getUserInfo method signature
        $getUserInfoMethod = $reflection->getMethod('getUserInfo');
        $this->assertTrue($getUserInfoMethod->isPublic());
        $this->assertEquals(1, $getUserInfoMethod->getNumberOfRequiredParameters());

        // Test sendMailAsUser method signature
        $sendMailMethod = $reflection->getMethod('sendMailAsUser');
        $this->assertTrue($sendMailMethod->isPublic());
        $this->assertEquals(4, $sendMailMethod->getNumberOfRequiredParameters());

        // Test fetchMails method signature
        $fetchMailsMethod = $reflection->getMethod('fetchMails');
        $this->assertTrue($fetchMailsMethod->isPublic());
        $this->assertEquals(2, $fetchMailsMethod->getNumberOfRequiredParameters());
    }
}
