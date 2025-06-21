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
	 * Konstruktor zur Initialisierung der Graph-Daten aus dem OAuth2-Response-Array.
	 *
	 * @param array $data Das assoziative Array der Graph-Response.
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
	 * Vergleicht die Graph-Daten mit den Feldern der User-Klasse und aktualisiert den User,
	 * sofern Unterschiede festgestellt werden.
	 *
	 * Es wird ein Mapping verwendet, um die Graph-Felder den entsprechenden User-Feldern zuzuordnen.
	 * Bei Unterschieden wird die update()-Methode der User-Klasse aufgerufen.
	 *
	 * @param \User $user Eine Instanz der User-Klasse.
	 */
	public function compareAndUpdateUser(\User $user): void {
		// Mapping von Graph-Daten zu User-Feldern
		$mapping = [
			'id'                => 'AzureObjectId',   // Graph "id" entspricht dem Azure Object Id
			'userPrincipalName' => 'Upn',
			'displayName'       => 'DisplayName',
			'givenName'         => 'Name',            // Graph "givenName" als Vorname
			'surname'           => 'Surname',
			'jobTitle'          => 'Title',
			'mail'              => 'Mail',
			'mobilePhone'       => 'MobilePhone',
			'businessPhones'    => 'BusinessPhones',  // Als kommaseparierten String gespeichert
			'officeLocation'    => 'OfficeLocation'
		];

		foreach ($mapping as $graphField => $userField) {
			// Hole den Graph-Wert. Für businessPhones wird der Array-Inhalt als kommaseparierter String zusammengefügt.
			$graphValue = ($graphField === 'businessPhones')
				? json_encode($this->businessPhones)
				: $this->{$graphField};

			// Hole den aktuellen Wert aus der User-Instanz.
			$userValue = $user->{$userField};

			// Vergleiche und aktualisiere, wenn der Wert unterschiedlich ist.
			if ($userValue !== $graphValue) {
				$user->update($userField, $graphValue);
			}
		}
	}
}
