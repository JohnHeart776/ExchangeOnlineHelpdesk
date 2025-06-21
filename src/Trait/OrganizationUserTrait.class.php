<?php

trait OrganizationUserTrait
{

	/**
	 * @return bool
	 */
	public function hasPhoto(): bool
	{
		return !empty($this->Photo);
	}

	/**
	 * @return false|string
	 */
	public function getPhotoDecoded(): false|string
	{
		return base64_decode($this->Photo);
	}

	public function updateFromGraphUser(\Struct\GraphUser $graphUser)
	{
		if ($graphUser->mail != $this->Mail)
			$this->update("Mail", $graphUser->mail);

		if ($graphUser->user_principal_name != $this->UserPrincipalName)
			$this->update("UserPrincipalName", $graphUser->user_principal_name);

		if ($graphUser->account_enabled != $this->AccountEnabled)
			$this->update("AccountEnabled", $graphUser->account_enabled);

		if ($graphUser->job_title != $this->JobTitle)
			$this->update("JobTitle", $graphUser->job_title);

		if ($graphUser->mobile_phone != $this->MobilePhone)
			$this->update("MobilePhone", $graphUser->mobile_phone);

		if ($graphUser->office_location != $this->OfficeLocation)
			$this->update("OfficeLocation", $graphUser->office_location);

		if ($graphUser->employee_id != $this->EmployeeId)
			$this->update("EmployeeId", $graphUser->employee_id);

		if ($graphUser->department != $this->Department)
			$this->update("Department", $graphUser->department);

		if ($graphUser->company_name != $this->CompanyName)
			$this->update("CompanyName", $graphUser->company_name);

		if ($graphUser->business_phones != $this->BusinessPhones)
			$this->update("BusinessPhones", $graphUser->business_phones);

		return $this->spawn();

	}

	public function updatePhotoFromGraphUserImage(\Struct\GraphUserImage $graphUserImage)
	{
		$this->update("Photo", $graphUserImage->base64);
	}

	public function getAgentLink()
	{
		return "/agent/organizationuser/" . $this->GetGuid();
	}

	public function getSafeUserPrincipalNameForAvatar()
	{
		return str_replace(
			["#"],
			["hash"],
			$this->getUserPrincipalName()
		);
	}

	/**
	 * @return string
	 */
	#[Pure] public function getAvatarLink(): string
	{
		return "/api/organizationuser/{$this->getGuid()}/image.jpg";
	}

	/**
	 * @return string
	 */
	public function getPhotoLink(): string
	{
		return $this->getAvatarLink();
	}

	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"userPrincipalName" => $this->getUserPrincipalName(),
			"displayName" => $this->getDisplayName(),
			"mail" => $this->getMail(),
			"jobTitle" => $this->getJobTitle(),
			"mobilePhone" => $this->getMobilePhone(),
			"officeLocation" => $this->getOfficeLocation(),
			"employeeId" => $this->getEmployeeId(),
			"department" => $this->getDepartment(),
			"photoLink" => $this->getPhotoLink(),
		];
	}

	/**
	 * @return ?TicketAssociate[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getTicketAssociates(): ?array
	{
		return TicketAssociateController::searchBy("OrganizationUserId", $this->getOrganizationUserIdAsInt());
	}

	public function countTicketAssociates(): int
	{
		return count($this->getTicketAssociates());
	}

	public function sendMailMessage(string $subject, string $body)
	{
		return MailHelper::sendStyledMailFromSystemAccount(null, $this->getMail(), $subject, $body);
	}

	/**
	 * @return array
	 */
	public function getBusinessPhonesDecoded(): array
	{
		return json_decode($this->BusinessPhones, true);
	}


}