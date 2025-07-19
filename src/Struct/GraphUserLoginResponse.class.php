<?php

namespace Struct;

class GraphUserLoginResponse {
	public ?string $odataContext;
	public array $businessPhones;
	public ?string $displayName;
	public ?string $givenName;
	public ?string $jobTitle;
	public ?string $mail;
	public ?string $mobilePhone;
	public ?string $officeLocation;
	public ?string $preferredLanguage;
	public ?string $surname;
	public ?string $userPrincipalName;
	public ?string $id;

	/**
	 * Constructor to initialize Graph data from OAuth2 response array.
	 *
	 * @param array $data The associative array of the Graph response.
	 */
	public function __construct(array $data) {
		$this->odataContext      = $data['@odata.context']    ?? null;
		$this->businessPhones    = isset($data['businessPhones']) && is_array($data['businessPhones'])
			? $data['businessPhones'] : [];
		$this->displayName       = $data['displayName']       ?? null;
		$this->givenName         = $data['givenName']         ?? null;
		$this->jobTitle          = $data['jobTitle']          ?? null;
		$this->mail              = $data['mail']              ?? null;
		$this->mobilePhone       = $data['mobilePhone']       ?? null;
		$this->officeLocation    = $data['officeLocation']    ?? null;
		$this->preferredLanguage = $data['preferredLanguage'] ?? null;
		$this->surname           = $data['surname']           ?? null;
		$this->userPrincipalName = $data['userPrincipalName'] ?? null;
		$this->id                = $data['id']                ?? null;
	}

	/**
	 * Compares Graph data with User class fields and updates the user
	 * if differences are detected.
	 *
	 * A mapping is used to associate Graph fields with corresponding User fields.
	 * If differences are found, the update() method of the User class is called.
	 *
	 * @param \User $user An instance of the User class.
	 */
	public function compareAndUpdateUser(\User $user): void {
		// Mapping von Graph-Daten zu User-Feldern
		$mapping = [
			'id' => 'AzureObjectId',   // Graph "id" corresponds to Azure Object Id
			'userPrincipalName' => 'Upn',
			'displayName'       => 'DisplayName',
			'givenName'         => 'Name',            // Graph "givenName" als Vorname
			'surname'           => 'Surname',
			'jobTitle'          => 'Title',
			'mail'              => 'Mail',
			'mobilePhone'       => 'MobilePhone',
			'businessPhones' => 'BusinessPhones',  // Stored as comma-separated string
			'officeLocation'    => 'OfficeLocation'
		];

		foreach ($mapping as $graphField => $userField) {
			// Hole den Graph-Wert. Für businessPhones wird der Array-Inhalt als kommaseparierter String zusammengefügt.
			$graphValue = ($graphField === 'businessPhones')
				? json_encode($this->businessPhones)
				: $this->{$graphField};

			// Hole den aktuellen Wert aus der User-Instanz.
			$userValue = $user->{$userField};

			// Compare and update if the value is different.
			if ($userValue !== $graphValue) {
				$user->update($userField, $graphValue);
			}
		}
	}
}
