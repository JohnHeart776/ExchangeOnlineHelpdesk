<?php

use JetBrains\PhpStorm\NoReturn;

class ApiResponse
{

	public function __construct(
		public bool   $status,
		public string $message,
		public ?array $data,
	)
	{
		// ...
	}

	public static function return($status, $message = "", $data = null): string
	{
		$a = new self($status, $message, $data);
		Header("Content-Type: application/json");
		return json_encode($a->toJsObject());
	}

	public static function die($status, $message = "", $data = null)
	{
		echo self::return($status, $message, $data);
		die();
	}

	public static function echoRaw(mixed $data = null)
	{
		Header("Content-Type: application/json");
		echo json_encode($data);
		die();
	}


	public function toJsObject()
	{
		return [
			"status" => $this->status,
			"message" => $this->message,
			"data" => $this->data,
		];
	}

}
