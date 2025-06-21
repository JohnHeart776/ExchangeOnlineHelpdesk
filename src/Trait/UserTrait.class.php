<?php

trait UserTrait
{

	public function fromGraphLoginResponseData(array $orgInfo, array $graphData): self
	{
		$this->TenantId = $orgInfo["tenantId"];
		$this->AzureObjectId = $graphData['id'];
		$this->Upn = $graphData['userPrincipalName'];
		$this->DisplayName = $graphData['displayName'];
		$this->Name = $graphData['givenName'];
		$this->Surname = $graphData['surname'];
		$this->Title = $graphData['jobTitle'];
		$this->Mail = $graphData['mail'];
		$this->Telephone = $graphData['telephoneNumber'];
		$this->OfficeLocation = $graphData['officeLocation'];
		$this->CompanyName = $graphData['companyName'];
		$this->MobilePhone = $graphData['mobilePhone'];
		$this->BusinessPhones = json_encode($graphData['businessPhones']);
		$this->AccountEnabled = ($graphData['accountEnabled'] ?? true) ? 1 : 0;
		$this->UserRole = Config::getConfigValueFor("authentication.newuser.accesslevel.default", "guest");
		return $this;
	}

	public function hasUserImage(): bool
	{
		return $this->getUserImage() !== null;
	}

	/**
	 * @return UserImage|null
	 * @throws Exception
	 */
	public function getUserImage(): UserImage|null
	{
		return UserImageController::searchOneBy("UserId", $this->getUserId());
	}

	public function getLink()
	{
		return "/user/" . $this->getGuid();
	}

	public function getAdminLink()
	{
		return "/admin/user/" . $this->getGuid();
	}

	public function getUserImageLink()
	{
		return "/api/user/" . $this->getGuid() . "/image.jpg";
	}

	public function isAgent()
	{
		if ($this->isAdmin())
			return true;

		return $this->GetUserRole() == "agent";
	}

	public function isAdmin()
	{
		return $this->GetUserRole() == "admin";
	}

	/**
	 * @param ?DateTime $cacheTime
	 * @return ?AgentWrapper
	 */
	public function getAgentWrapper(?DateTime $cacheTime = null): ?AgentWrapper
	{
		if (!$this->isAgent())
			return null;
		if (!$cacheTime)
			$cacheTime = new DateTime("-2 years");
		return new AgentWrapper($this, $cacheTime);
	}

	/**
	 * @return array
	 */
	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->getDisplayName(),
			"email" => $this->getMail(),
			"role" => $this->getUserRole(),
			"image" => $this->getUserImageLink(),
		];
	}

	public function isEnabled(): bool
	{
		return $this->getEnabledAsInt() > 0;
	}

	/**
	 * @param string $subject
	 * @param string $body
	 * @return true
	 * @throws \Database\DatabaseQueryException
	 */
	public function sendMailMessage(string $subject, string $body)
	{
		return MailHelper::sendStyledMailFromSystemAccount(null, $this->getMail(), $subject, $body);
	}

	public function isUser(): bool
	{
		return $this->isValid();
	}

	public function getBusinessPhonesDecoded(): array
	{
		return json_decode($this->getBusinessPhones(), true);
	}

	/**
	 * @return OrganizationUser|null
	 * @throws \Database\DatabaseQueryException
	 */
	public function getOrganizationUser(): ?OrganizationUser
	{
		return OrganizationUserController::searchOneBy("UserPrincipalName", $this->getUpn());
	}


}