<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;
use Core\Login;
use Application\User;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear any existing session data
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    protected function tearDown(): void
    {
        // Clean up session data after each test
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        parent::tearDown();
    }

    public function testIsLoggedInReturnsFalseWhenNoSession(): void
    {
        $this->assertFalse(Login::isLoggedIn());
    }

    public function testGetUserReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(Login::getUser());
    }

    public function testGetAccessTokenReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(Login::getAccessToken());
    }

    public function testGetTokenRefreshThresholdReturnsInteger(): void
    {
        $threshold = Login::getTokenRefreshThreshold();
        $this->assertIsInt($threshold);
        $this->assertGreaterThan(0, $threshold);
    }

    public function testInitSessionStartsSession(): void
    {
        Login::initSession();
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    public function testIsGuestReturnsTrueWhenNotLoggedIn(): void
    {
        $this->assertTrue(Login::isGuest());
    }

    public function testIsUserReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse(Login::isUser());
    }

    public function testIsAgentReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse(Login::isAgent());
    }

    public function testIsAdminReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse(Login::isAdmin());
    }

    public function testRequireLoginRedirectsWhenNotLoggedIn(): void
    {
        $this->expectOutputRegex('/Location:/');
        Login::requireLogin();
    }

    public function testRequireIsGuestDoesNotThrowWhenNotLoggedIn(): void
    {
        // Should not throw any exception when user is guest
        Login::requireIsGuest();
        $this->assertTrue(true); // Assert that we reach this point
    }

    public function testRequireIsUserRedirectsWhenNotLoggedIn(): void
    {
        $this->expectOutputRegex('/Location:/');
        Login::requireIsUser();
    }

    public function testRequireIsAgentRedirectsWhenNotLoggedIn(): void
    {
        $this->expectOutputRegex('/Location:/');
        Login::requireIsAgent();
    }

    public function testRequireIsAdminRedirectsWhenNotLoggedIn(): void
    {
        $this->expectOutputRegex('/Location:/');
        Login::requireIsAdmin();
    }

    public function testBringToDashboardOutputsRedirect(): void
    {
        $this->expectOutputRegex('/Location:.*dashboard\.php/');
        Login::bringToDashboard();
    }

    public function testBringToLoginOutputsRedirect(): void
    {
        $this->expectOutputRegex('/Location:.*login\.php/');
        Login::bringToLogin();
    }

    public function testBringToLoginWithRedirectTarget(): void
    {
        $this->expectOutputRegex('/Location:.*login\.php\?redirect_target=test/');
        Login::bringToLogin('test');
    }

    public function testBringToLoginWithMessage(): void
    {
        $this->expectOutputRegex('/Location:.*login\.php.*message=test/');
        Login::bringToLogin(null, false, 'test');
    }

    public function testLogoutClearsSession(): void
    {
        // Start a session first
        Login::initSession();
        $_SESSION['user_id'] = 123;
        $_SESSION['access_token'] = 'test_token';
        
        // Logout should clear session
        Login::logout();
        
        $this->assertArrayNotHasKey('user_id', $_SESSION ?? []);
        $this->assertArrayNotHasKey('access_token', $_SESSION ?? []);
    }
}