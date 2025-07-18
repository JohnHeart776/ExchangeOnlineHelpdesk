<?php

namespace Database;

use Exception;
use PDO;
use mysqli;
use PDOException;
use PDOStatement;

// Basis-Exception für alle Datenbank-bezogenen Fehler
class DatabaseException extends Exception
{
}

// Exception für Verbindungsfehler (sowohl MySQLi als auch PDO)
class DatabaseConnectException extends DatabaseException
{
}

// Exception für Fehler beim Setzen des Zeichensatzes
class DatabaseCharsetException extends DatabaseException
{
}

// Exception für Fehler bei Query-Ausführungen
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

		// MySQLi-Verbindung herstellen
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
	 * Gibt die MySQLi-Verbindung zurück.
	 *
	 * @return mysqli|null
	 */
	public function getLink(): ?mysqli
	{
		return $this->link;
	}

	/**
	 * Erstellt die PDO-Verbindung bei Bedarf (lazy loading).
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
	 * Gibt die PDO-Verbindung zurück (erstellt sie bei Bedarf).
	 *
	 * @return PDO|null
	 */
	public function getPDOConnection(): ?PDO
	{
		$this->initializePDOConnection();
		return $this->pdo;
	}

	/**
	 * Schließt die MySQLi-Verbindung.
	 */
	public function close(): void
	{
		if ($this->link) {
			$this->link->close();
		}
	}

	/**
	 * Schließt die PDO-Verbindung, indem der interne PDO-Handler auf null gesetzt wird.
	 */
	public function closePDO(): void
	{
		$this->pdo = null;
	}

	/**
	 * Führt einen Query über MySQLi aus.
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
	 * Führt einen Query über PDO aus.
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
	 * Führt eine SELECT-Abfrage über MySQLi aus und gibt das Ergebnis als assoziatives Array zurück.
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
	 * Führt eine SELECT-Abfrage über PDO aus und gibt das Ergebnis als assoziatives Array zurück.
	 * Unterstützt Prepared Statements, wenn ein Parameter-Array übergeben wird.
	 *
	 * @param string $_q     Die SQL-Abfrage.
	 * @param array  $params Optional: Parameter für das Prepared Statement.
	 * @param bool   $single Gibt an, ob nur ein einzelner Datensatz zurückgegeben werden soll.
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
	 * Escaped einen String für MySQLi.
	 *
	 * @param string $_string
	 * @return string
	 */
	public function filter($_string)
	{
		return $this->link->real_escape_string($_string);
	}

	/**
	 * Escaped einen String für PDO.
	 *
	 * Hinweis: PDO::quote gibt den String mit umschließenden Anführungszeichen zurück,
	 * diese werden hier entfernt.
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
	 * Gibt die Anzahl der Datensätze einer MySQLi-Query zurück.
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
	 * Gibt die Anzahl der Datensätze einer PDO-Query zurück.
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
	 * Gibt die ID des zuletzt eingefügten Datensatzes zurück.
	 * @return string|int|null Die ID des zuletzt eingefügten Datensatzes oder null, falls keine Verbindung besteht.
	 */
	public function lastInsertIdFromMysqli(): string|int|null
	{
		if ($this->link) {
			return $this->link->insert_id;
		}
		return null;
	}

	/**
	 * Gibt die ID des zuletzt eingefügten Datensatzes zurück.
	 * @return string|int|null Die ID des zuletzt eingefügten Datensatzes oder null, falls keine Verbindung besteht.
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
