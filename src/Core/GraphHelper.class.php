<?php

use Auth\GraphCertificateAuthenticator;
use Client\GraphClient;

class GraphHelper
{


	/**
	 * @return GraphClient
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getApplicationAuthenticatedGraph(): GraphClient
	{

		$auth = new GraphCertificateAuthenticator(
			tenantId: Config::getConfigValueFor('tenantId'),
			clientId: Config::getConfigValueFor('application.clientId'),
			cert: Config::getConfigValueFor('application.certificate'),
			key: Config::getConfigValueFor('application.certificateKey'),
			keyPass: Config::getConfigValueFor('application.certificateKeyPassword')
		);

		return new GraphClient($auth);
	}
}
