<?php

namespace Struct;

use OrganizationUser;

class GraphUser
{
	public string $guid;
	public string $azure_id;
	public string $display_name;
	public string $user_principal_name;
	public ?string $mail = null;
	public ?string $given_name = null;
	public ?string $surname = null;
	public ?string $job_title = null;
	public ?string $department = null;
	public ?string $mobile_phone = null;
	public ?string $office_location = null;
	public ?string $company_name = null;
	public ?string $business_phones = null;
	public ?bool $account_enabled = null;
	public ?string $employee_id = null;
	public ?string $sam_account_name = null;


	public static function fromArray(array $data): static
	{
		$user = new self();
		$user->guid = \GuidHelper::generateGuid();
		$user->azure_id = $data['id'];
		$user->display_name = $data['displayName'] ?? '';
		$user->user_principal_name = $data['userPrincipalName'] ?? '';
		$user->mail = $data['mail'] ?? null;
		$user->given_name = $data['givenName'] ?? null;
		$user->surname = $data['surname'] ?? null;
		$user->job_title = $data['jobTitle'] ?? null;
		$user->department = $data['department'] ?? null;
		$user->mobile_phone = $data['mobilePhone'] ?? null;
		$user->office_location = $data['officeLocation'] ?? null;
		$user->company_name = $data['companyName'] ?? null;
		$user->business_phones = json_encode($data['businessPhones']);
		$user->account_enabled = $data['accountEnabled'] ?? null;
		$user->employee_id = $data['employeeId'] ?? null;
		$user->sam_account_name = $data['onPremisesSamAccountName'] ?? null;
		return $user;
	}

	/**
	 * @return OrganizationUser
	 */
	public function toOrganizationUser(): OrganizationUser
	{
		$orgUser = new OrganizationUser(0);

		$orgUser->AzureObjectId = $this->azure_id;
		$orgUser->DisplayName = $this->display_name;
		$orgUser->UserPrincipalName = $this->user_principal_name;
		$orgUser->Mail = $this->mail;
		$orgUser->GivenName = $this->given_name;
		$orgUser->Surname = $this->surname;
		$orgUser->JobTitle = $this->job_title;
		$orgUser->Department = $this->department;
		$orgUser->MobilePhone = $this->mobile_phone;
		$orgUser->OfficeLocation = $this->office_location;
		$orgUser->CompanyName = $this->company_name;
		$orgUser->BusinessPhones = json_encode($this->business_phones);
		$orgUser->AccountEnabled = $this->account_enabled;
		$orgUser->EmployeeId = $this->employee_id;
		$orgUser->SamAccountName = $this->sam_account_name;

		return $orgUser;
	}

}