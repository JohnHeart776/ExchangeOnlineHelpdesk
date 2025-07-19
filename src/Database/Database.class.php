<?php

namespace Database;

use Exception;
use PDO;
use mysqli;
use PDOException;
use PDOStatement;

// Base exception for all database-related errors
class DatabaseException extends Exception
{
}

// Exception for connection errors (both MySQLi and PDO)
class DatabaseConnectException extends DatabaseException
{
}

// Exception for errors when setting the character set
class DatabaseCharsetException extends DatabaseException
{
}

// Exception for query execution errors
class DatabaseQueryException extends DatabaseException
{
	protected string $query;
	protected ?Exception $originalException;

	/**
	 * @param string         $message
	 * @param string         $query
	 * @param Exception|null $originalException
	 * @param int            $code
	 * @param Exception|null $previous
	 */
	public function __construct(string $message, string $query, ?Exception $originalException = null, int $code = 0, ?Exception $previous = null)
	{
		$this->query = $query;
		$this->originalException = $originalException;
		parent::__construct($message, $code, $previous);
	}

	public function getQuery(): string
	{
		return $this->query;
	}

	public function getOriginalException(): ?Exception
	{
		return $this->originalException;
	}
}

class Database
{
	private static ?self $instance = null;
	private ?mysqli $link;
	private ?PDO $pdo;
	private array $dbConfig;

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct()
	{
		// Cache database configuration to avoid repeated getenv() calls
		$this->dbConfig = [
			'host' => getenv('DBHOST'),
			'user' => getenv('DBUSER'),
			'password' => getenv('DBPASSWORD'),
			'name' => getenv('DBNAME')
		];

		// Validate required environment variables
		if (!$this->dbConfig['host'] || !$this->dbConfig['user'] || !$this->dbConfig['name']) {
			throw new DatabaseConnectException("Missing required database environment variables (DBHOST, DBUSER, DBNAME)");
		}

		// Establish MySQLi connection
		$this->link = new mysqli($this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['password'], $this->dbConfig['name']);
		if ($this->link->connect_errno) {
			throw new DatabaseConnectException("MySQLi connection error: " . $this->link->connect_error);
		}

		if (!$this->link->set_charset("utf8mb4")) {
			throw new DatabaseCharsetException("Error loading character set utf8mb4: " . $this->link->error);
		}

		// PDO connection will be created lazily when first needed
		$this->pdo = null;
	}

	/**
	 * Returns the MySQLi connection.
	 *
	 * @return mysqli|null
	 */
	public function getLink(): ?mysqli
	{
		return $this->link;
	}

	/**
	 * Creates the PDO connection when needed (lazy loading).
	 *
	 * @return void
	 * @throws DatabaseConnectException
	 */
	private function initializePDOConnection(): void
	{
		if ($this->pdo !== null) {
			return;
		}

		// Use cached database configuration instead of repeated getenv() calls
		$dsn = "mysql:host={$this->dbConfig['host']};dbname={$this->dbConfig['name']};charset=utf8mb4";
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];

		try {
			$this->pdo = new PDO($dsn, $this->dbConfig['user'], $this->dbConfig['password'], $options);
		} catch (PDOException $ex) {
			throw new DatabaseConnectException("PDO connection error: " . $ex->getMessage(), "", $ex);
		}
	}

	/**
	 * Returns the PDO connection (creates it if needed).
	 *
	 * @return PDO|null
	 */
	public function getPDOConnection(): ?PDO
	{
		$this->initializePDOConnection();
		return $this->pdo;
	}

	/**
	 * Closes the MySQLi connection.
	 */
	public function close(): void
	{
		if ($this->link) {
			$this->link->close();
		}
	}

	/**
	 * Closes the PDO connection by setting the internal PDO handler to null.
	 */
	public function closePDO(): void
	{
		$this->pdo = null;
	}

	/**
	 * Executes a query via MySQLi.
	 *
	 * @param string $_q
	 * @return mixed
	 * @throws DatabaseQueryException
	 */
	public function query(string $_q): mixed
	{
		$result = $this->link->query($_q);
		if ($result === false) {
			throw new DatabaseQueryException("MySQLi query error: " . $this->link->error, $_q);
		}
		return $result;
	}

	/**
	 * Helper method to prepare and execute PDO statements
	 *
	 * @param string $_q
	 * @param array $params
	 * @return PDOStatement
	 * @throws DatabaseQueryException
	 */
	private function executePDOStatement(string $_q, array $params = []): PDOStatement
	{
		$this->initializePDOConnection();

		try {
			if (!empty($params)) {
				$stmt = $this->pdo->prepare($_q);
				if (!$stmt) {
					throw new DatabaseQueryException(
						"PDO prepare error: " . implode(", ", $this->pdo->errorInfo()),
						$_q
					);
				}
				$stmt->execute($params);
			} else {
				$stmt = $this->pdo->query($_q);
			}
		} catch (PDOException $ex) {
			throw new DatabaseQueryException("PDO query error: " . $ex->getMessage(), $_q, $ex);
		}
		return $stmt;
	}

	/**
	 * Executes a query via PDO.
	 *
	 * @param string $_q
	 * @return PDOStatement
	 * @throws DatabaseQueryException
	 */
	public function queryPDO(string $_q, array $params = []): PDOStatement
	{
		return $this->executePDOStatement($_q, $params);
	}

	/**
	 * Executes a SELECT query via MySQLi and returns the result as an associative array.
	 *
	 * @param string $_q
	 * @param bool   $single
	 * @return mixed
	 * @throws DatabaseQueryException
	 */
	public function get($_q, $single = false): mixed
	{
		$result = $this->link->query($_q);
		if ($result === false) {
			throw new DatabaseQueryException("MySQLi query error: " . $this->link->error, $_q);
		}

		if ($single) {
			$row = $result->fetch_assoc();
			$result->free();
			return $row;
		}

		$r = [];
		while ($row = $result->fetch_assoc()) {
			$r[] = $row;
		}
		$result->free();
		return $r;
	}

	/**
	 * Executes a SELECT query via PDO and returns the result as an associative array.
	 * Supports prepared statements when a parameter array is provided.
	 *
	 * @param string $_q     The SQL query.
	 * @param array  $params Optional: Parameters for the prepared statement.
	 * @param bool   $single Indicates whether only a single record should be returned.
	 * @return mixed
	 * @throws DatabaseQueryException
	 */
	public function getPDO(string $_q, array $params = [], bool $single = false): mixed
	{
		$stmt = $this->executePDOStatement($_q, $params);

		if ($single) {
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetchOnePDO(string $_q, array $params = []): mixed
	{
		return $this->getPDO($_q, $params, true);
	}

	/**
	 * Escapes a string for MySQLi.
	 *
	 * @param string $_string
	 * @return string
	 */
	public function filter($_string)
	{
		return $this->link->real_escape_string($_string);
	}

	/**
	 * Escapes a string for PDO.
	 *
	 * Note: PDO::quote returns the string with surrounding quotes,
	 * these are removed here.
	 *
	 * @param string $_string
	 * @return string
	 */
	public function filterPDO($_string): string
	{
		$this->initializePDOConnection();
		$quoted = $this->pdo->quote($_string);
		return substr($quoted, 1, -1);
	}

	/**
	 * Returns the number of records from a MySQLi query.
	 *
	 * @param string $_query
	 * @return int
	 * @throws DatabaseQueryException
	 */
	public function count($_query): int
	{
		$result = $this->link->query($_query);
		if ($result === false) {
			throw new DatabaseQueryException("MySQLi query error: " . $this->link->error, $_query);
		}
		$row_cnt = $result->num_rows;
		$result->free();
		return (int)$row_cnt;
	}

	/**
	 * Returns the number of records from a PDO query.
	 *
	 * @param string $_query
	 * @return int
	 * @throws DatabaseQueryException
	 */
	public function countPDO($_query): int
	{
		$this->initializePDOConnection();
		try {
			$stmt = $this->pdo->query($_query);
		} catch (PDOException $ex) {
			throw new DatabaseQueryException("PDO query error: " . $ex->getMessage(), $_query, $ex);
		}

		// Use rowCount() for better performance instead of fetching all rows
		// Note: For SELECT statements, rowCount() behavior varies by database
		// For more reliable counting, we could wrap the query in SELECT COUNT(*) FROM (...)
		$rowCount = $stmt->rowCount();

		// If rowCount() returns 0 or unreliable result for SELECT, fall back to fetching
		if ($rowCount === 0 && stripos(trim($_query), 'SELECT') === 0) {
			// For SELECT queries, count rows by iterating without storing data
			$count = 0;
			while ($stmt->fetch(PDO::FETCH_NUM)) {
				$count++;
			}
			return $count;
		}

		return $rowCount;
	}

	/**
	 * Returns the ID of the last inserted record.
	 * @return string|int|null The ID of the last inserted record or null if no connection exists.
	 */
	public function lastInsertIdFromMysqli(): string|int|null
	{
		if ($this->link) {
			return $this->link->insert_id;
		}
		return null;
	}

	/**
	 * Returns the ID of the last inserted record.
	 * @return string|int|null The ID of the last inserted record or null if no connection exists.
	 */
	public function lastInsertIdFromPdo(): string|int|null
	{
		$this->initializePDOConnection();
		if ($this->pdo) {
			return $this->pdo->lastInsertId();
		}
		return null;
	}


}
