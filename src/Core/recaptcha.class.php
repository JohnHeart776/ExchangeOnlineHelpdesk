<?php


class recaptcha
{

	/**
	 * @param string $secret
	 * @param string $clientResponse
	 * @param string $remoteIp
	 * @return ReCaptcha_Validation_Result
	 * @throws JsonException
	 */
	public static function ValidateV2(
		string $secret,
		string $clientResponse,
		string $remoteIp
	): ReCaptcha_Validation_Result
	{
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $clientResponse . "&remoteip=" . $remoteIp);
		$result = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

		$rvr = new ReCaptcha_Validation_Result($result->success);
		$rvr->version = 2;
		$rvr->data = $result;

		return $rvr;
	}

	/**
	 * @param string $secret
	 * @param string $response
	 * @param string $remoteIp
	 * @return ReCaptcha_Validation_Result
	 * @throws JsonException
	 */
	public static function ValidateV3(string $secret, string $response, string $remoteIp): ReCaptcha_Validation_Result
	{
		$post_data = http_build_query(
			[
				'secret' => $secret,
				'response' => $response,
				'remoteip' => $remoteIp,
			]
		);

		$opts = ['http' =>
			[
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data,
			],
		];

		$context = stream_context_create($opts);
		$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
		$result = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
		// s($result);
		$rvr = new ReCaptcha_Validation_Result($result->success);
		$rvr->version = 3;
		$rvr->data = $result;

		return $rvr;
	}

}
