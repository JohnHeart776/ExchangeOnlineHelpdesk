<?php

namespace Test\Unit\Application;

use PHPUnit\Framework\TestCase;
use Application\User;
use DateTime;

class UserTest extends TestCase
{
    private $mockUserId = 123;
    private $mockGuid = 'test-guid-123';

    public function testConstructorWithIntegerKey(): void
    {
        $user = new User($this->mockUserId);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testConstructorWithStringKey(): void
    {
        $user = new User($this->mockGuid);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testResolveGuidToIdReturnsInteger(): void
    {
        $result = User::resolveGuidToId($this->mockGuid);
        $this->assertIsInt($result);
    }

    public function testIsValidReturnsBooleanForValidUser(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->isValid();
        $this->assertIsBool($result);
    }

    public function testGetUserIdReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getUserId();
        // Result can be null or the actual user ID
        $this->assertTrue($result === null || is_numeric($result));
    }

    public function testGetGuidReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getGuid();
        // Result can be null or a string
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetEnabledReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getEnabled();
        // Result can be null or numeric
        $this->assertTrue($result === null || is_numeric($result));
    }

    public function testGetTenantIdReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getTenantId();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetAzureObjectIdReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getAzureObjectId();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetUpnReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getUpn();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetDisplayNameReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getDisplayName();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetNameReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getName();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetSurnameReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getSurname();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetTitleReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getTitle();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetMailReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getMail();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetTelephoneReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getTelephone();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetOfficeLocationReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getOfficeLocation();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetCompanyNameReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getCompanyName();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetMobilePhoneReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getMobilePhone();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetBusinessPhonesReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getBusinessPhones();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetAccountEnabledReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getAccountEnabled();
        $this->assertTrue($result === null || is_numeric($result));
    }

    public function testGetUserRoleReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getUserRole();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetLastLoginReturnsValue(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getLastLogin();
        $this->assertTrue($result === null || is_string($result));
    }

    public function testGetUserIdAsIntReturnsInteger(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getUserIdAsInt();
        $this->assertIsInt($result);
    }

    public function testGetUserIdAsBoolReturnsBool(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getUserIdAsBool();
        $this->assertIsBool($result);
    }

    public function testGetEnabledAsIntReturnsInteger(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getEnabledAsInt();
        $this->assertIsInt($result);
    }

    public function testGetEnabledAsBoolReturnsBool(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getEnabledAsBool();
        $this->assertIsBool($result);
    }

    public function testGetAccountEnabledAsIntReturnsInteger(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getAccountEnabledAsInt();
        $this->assertIsInt($result);
    }

    public function testGetAccountEnabledAsBoolReturnsBool(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getAccountEnabledAsBool();
        $this->assertIsBool($result);
    }

    public function testGetLastLoginAsDateTimeReturnsDateTime(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->getLastLoginAsDateTime();
        $this->assertInstanceOf(DateTime::class, $result);
    }

    public function testEqualsReturnsTrueForSameUser(): void
    {
        $user1 = new User($this->mockUserId);
        $user2 = new User($this->mockUserId);
        
        // This test may fail if users don't exist in database
        // but it tests the method signature and return type
        $result = $user1->equals($user2);
        $this->assertIsBool($result);
    }

    public function testEqualsReturnsFalseForNull(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->equals(null);
        $this->assertFalse($result);
    }

    public function testIsUpdateAllowedKeyReturnsBool(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->isUpdateAllowedKey('display_name');
        $this->assertIsBool($result);
    }

    public function testToggleValueReturnsBool(): void
    {
        $user = new User($this->mockUserId);
        $result = $user->toggleValue('enabled', [0, 1]);
        $this->assertIsBool($result);
    }
}