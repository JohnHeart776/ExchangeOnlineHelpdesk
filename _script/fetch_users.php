<?php

require_once __DIR__ . '/../src/bootstrap.php';
global $d;

use Auth\GraphCertificateAuthenticator;
use Client\GraphClient;

try {

	$logger = Logger::getInstance(
		Config::getConfigValueFor('log.dir'),
		Config::getConfigValueFor('log.retention')
	);
	$logger->enableScreenOutput();

	$logger->log("== START fetch_all_users.php ==");

	$client = GraphHelper::getApplicationAuthenticatedGraph();

	$countNew = 0;
	$countUpdated = 0;
	$skipped = 0;

	$nextUrl = 'https://graph.microsoft.com/v1.0/users?$top=100&$select=id,displayName,userPrincipalName,mail,givenName,surname,jobTitle,department,companyName,mobilePhone,officeLocation,businessPhones,accountEnabled';

	$logger->log("Get next Users page URL: $nextUrl");

	while ($nextUrl !== null) {
		$logger->log("Fetching next page: $nextUrl");

		$response = $client->fetchUserPage($nextUrl);
		$logger->log("Erhaltene Benutzer: " . count($response['value']));

		foreach ($response['value'] as $item) {

			$graphUser = \Struct\GraphUser::fromArray($item);
			$logger->log("Bearbeite GraphUser: " . $graphUser->azure_id);

			$userExists = OrganizationUserController::exist("AzureObjectId", $graphUser->azure_id);

			if ($userExists) {

				$logger->log("\t~ Already exists");
				$organizationUser = OrganizationUserController::searchBy("AzureObjectId", $graphUser->azure_id, true);
				$logger->log("\tMap zu " . $organizationUser->DisplayName);

				$organizationUser->updateFromGraphUser($graphUser);
				$logger->log("\tUpdated from GraphUser");

			} else {

				$logger->log("\t+ New");

				$newOrganizationUser = $graphUser->toOrganizationUser();
				$logger->log("\tNew OrganizationUser " . $newOrganizationUser->DisplayName);

				$organizationUser = OrganizationUserController::save($newOrganizationUser);
				$logger->log("\tSaved OrganizationUser");
			}

			// Bild des Users immer updaten
			try {
				if (!empty($organizationUser->UserPrincipalName)) {
					$image = $client->getUserImage($organizationUser->UserPrincipalName);
					if ($image?->base64) {
						$organizationUser->updatePhotoFromGraphUserImage($image);
						$logger->log("\t\t+ Updated Photo from GraphUserImage");
					}
				}
			} catch (Exception $e) {
				$logger->log("⚠️ No image for {$organizationUser->DisplayName}: " . $e->getMessage());
			}


		}

		$nextUrl = $response['@odata.nextLink'] ?? null;

		if ($nextUrl) {
			$logger->log("➡️ Continue to next page.");
		} else {
			$logger->log("🏁 All pages loaded.");
		}
	}

	$logger->log("== END fetch_all_users.php ($countNew new, $countUpdated updated, $skipped skipped) ==\n");

} catch (Exception $e) {
	$error = "❌ Error: " . $e->getMessage();
	echo "$error\n";
	Logger::getInstance(
		Config::getConfigValueFor('log.dir')
	)->log($error);
}
