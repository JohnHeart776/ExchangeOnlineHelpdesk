<?php

namespace Test\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Database\Database;
use Database\DatabaseException;
use Database\DatabaseConnectException;
use Database\DatabaseQueryException;
use PDO;
use PDOStatement;
use mysqli;
use Exception;

/**
 * Unit tests for Database class
 */
class DatabaseTest extends TestCase
{
    private Database $database;
    private MockObject $mockMysqli;
    private MockObject $mockPdo;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock objects
        $this->mockMysqli = $this->createMock(mysqli::class);
        $this->mockPdo = $this->createMock(PDO::class);
        
        // Get database instance
        $this->database = Database::getInstance();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up singleton instance for next test
        $reflection = new \ReflectionClass(Database::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(Database::class, $instance1);
    }

    public function testDatabaseExceptionHierarchy(): void
    {
        $this->assertInstanceOf(Exception::class, new DatabaseException());
        $this->assertInstanceOf(DatabaseException::class, new DatabaseConnectException());
        $this->assertInstanceOf(DatabaseException::class, new DatabaseQueryException('test', 'SELECT 1', null));
    }

    public function testDatabaseQueryExceptionStoresQueryAndOriginalException(): void
    {
        $originalException = new Exception('Original error');
        $query = 'SELECT * FROM users';
        $message = 'Query failed';
        
        $exception = new DatabaseQueryException($message, $query, $originalException);
        
        $this->assertEquals($query, $exception->getQuery());
        $this->assertSame($originalException, $exception->getOriginalException());
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testFilterMethodSanitizesInput(): void
    {
        $input = "test'string\"with<script>alert('xss')</script>";
        $filtered = $this->database->filter($input);
        
        // The filter method should escape dangerous characters
        $this->assertNotEquals($input, $filtered);
        $this->assertStringNotContainsString('<script>', $filtered);
    }

    public function testFilterPDOMethodSanitizesInput(): void
    {
        $input = "test'string\"with<script>alert('xss')</script>";
        $filtered = $this->database->filterPDO($input);
        
        // The filterPDO method should escape dangerous characters
        $this->assertNotEquals($input, $filtered);
        $this->assertStringNotContainsString('<script>', $filtered);
    }

    public function testQueryPDOWithValidParameters(): void
    {
        // Mock PDOStatement
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->with(['param1' => 'value1'])
                     ->willReturn(true);

        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with('SELECT * FROM users WHERE id = :param1')
                ->willReturn($mockStatement);

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $result = $this->database->queryPDO('SELECT * FROM users WHERE id = :param1', ['param1' => 'value1']);
        
        $this->assertSame($mockStatement, $result);
    }

    public function testQueryPDOThrowsExceptionOnFailure(): void
    {
        $this->expectException(DatabaseQueryException::class);
        
        // Mock PDO to throw exception
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->willThrowException(new \PDOException('Connection failed'));

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $this->database->queryPDO('SELECT * FROM users', []);
    }

    public function testGetPDOWithSingleResultReturnsOneRecord(): void
    {
        // Mock PDOStatement
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
        $mockStatement->expects($this->once())
                     ->method('fetch')
                     ->with(PDO::FETCH_ASSOC)
                     ->willReturn(['id' => 1, 'name' => 'Test User']);

        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->willReturn($mockStatement);

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $result = $this->database->getPDO('SELECT * FROM users WHERE id = :id', ['id' => 1], true);
        
        $this->assertEquals(['id' => 1, 'name' => 'Test User'], $result);
    }

    public function testGetPDOWithMultipleResultsReturnsArray(): void
    {
        $expectedResults = [
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2']
        ];

        // Mock PDOStatement
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
        $mockStatement->expects($this->once())
                     ->method('fetchAll')
                     ->with(PDO::FETCH_ASSOC)
                     ->willReturn($expectedResults);

        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->willReturn($mockStatement);

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $result = $this->database->getPDO('SELECT * FROM users', [], false);
        
        $this->assertEquals($expectedResults, $result);
    }

    public function testCountPDOReturnsCorrectCount(): void
    {
        // Mock PDOStatement
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
        $mockStatement->expects($this->once())
                     ->method('fetchColumn')
                     ->willReturn('5');

        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with('SELECT COUNT(*) FROM users')
                ->willReturn($mockStatement);

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $result = $this->database->countPDO('SELECT COUNT(*) FROM users');
        
        $this->assertEquals(5, $result);
    }

    public function testLastInsertIdFromPdoReturnsCorrectId(): void
    {
        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->expects($this->once())
                ->method('lastInsertId')
                ->willReturn('123');

        // Use reflection to set the PDO connection
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        $result = $this->database->lastInsertIdFromPdo();
        
        $this->assertEquals('123', $result);
    }

    public function testClosePDOSetsConnectionToNull(): void
    {
        // Set a mock PDO connection
        $mockPdo = $this->createMock(PDO::class);
        $reflection = new \ReflectionClass($this->database);
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $mockPdo);

        // Verify PDO is set
        $this->assertNotNull($pdoProperty->getValue($this->database));

        // Close PDO connection
        $this->database->closePDO();

        // Verify PDO is null
        $this->assertNull($pdoProperty->getValue($this->database));
    }
}