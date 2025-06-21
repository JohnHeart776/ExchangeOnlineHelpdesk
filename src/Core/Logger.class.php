<?php


class Logger
{
	private static ?Logger $instance = null;
	private string $logDir;
	private string $logFile;
	private bool $logToScreen = false;

	public static function getInstance(string $logDir = __DIR__ . '/../../../log', int $retentionDays = 14): Logger
	{
		if (self::$instance === null) {
			self::$instance = new self($logDir, $retentionDays);
		}
		return self::$instance;
	}

	private function __construct(string $logDir, int $retentionDays)
	{
		$this->logDir = rtrim($logDir, '/');
		$this->ensureLogDirectoryExists();
		$this->cleanupOldLogs($retentionDays);

		$date = date('Ymd');
		$this->logFile = "{$this->logDir}/log_{$date}.log";
	}

	/**
	 * Optional aktivieren, um Logs auch auf dem Bildschirm anzuzeigen
	 */
	public function enableScreenOutput(): void
	{
		$this->logToScreen = true;
	}

	public function log(string $message): void
	{
		$timestamp = date('Y-m-d H:i:s');
		$line = "[$timestamp] $message\n";
		file_put_contents($this->logFile, $line, FILE_APPEND);

		if ($this->logToScreen) {
			echo $line;
		}
	}

	public function debug(string $title, $data): void
	{
		$timestamp = date('Y-m-d H:i:s');
		$encoded = print_r($data, true);
		$line = "[$timestamp] $title:\n$encoded\n";
		file_put_contents($this->logFile, $line, FILE_APPEND);

		if ($this->logToScreen) {
			echo $line;
		}
	}

	public function getLogFilePath(): string
	{
		return $this->logFile;
	}

	private function ensureLogDirectoryExists(): void
	{
		if (!is_dir($this->logDir)) {
			mkdir($this->logDir, 0777, true);
		}
	}

	private function cleanupOldLogs(int $days): void
	{
		$files = glob($this->logDir . '/log_*.log');
		$now = time();
		foreach ($files as $file) {
			if (is_file($file) && filemtime($file) < ($now - ($days * 86400))) {
				unlink($file);
			}
		}
	}

}
