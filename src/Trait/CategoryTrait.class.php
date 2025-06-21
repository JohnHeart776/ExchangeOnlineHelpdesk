<?php

trait CategoryTrait
{

	/**
	 * @return string
	 */
	public function getAdminLink(): string
	{
		return "/admin/category/" . $this->getGuid() . "/";
	}


	/**
	 * @return Ticket[]
	 * @throws \Database\DatabaseQueryException
	 */
	public function getAllTicketsOfThisCategory(): array
	{
		return TicketController::searchBy("CategoryId", $this->getCategoryIdAsInt());
	}

	public function toJsonObject(): array
	{
		return [
			"guid" => $this->getGuid(),
			"internalName" => $this->getInternalName(),
			"publicName" => $this->getPublicName(),
			"color" => $this->getColor(),
			"icon" => $this->getIcon(),
			"markup" => "<span class=\"text-".$this->getColor()."\"><i class=\"fas ".$this->getIcon()."\"></i> ".$this->getPublicName()."</span>",
		];
	}

	public function getName()
	{
		return $this->getPublicName();
	}

}