<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class UserPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \App\UserModule\Controls\UserListGrid\IUserListGridControlFactory
	 */
	private $userListGridFactory;

	/**
	 * @var \App\UserModule\Controls\AddAdmin\IAddAdminControlFactory
	 */
	private $addAdminControlFactory;

	/**
	 * @var \App\UserModule\Controls\AddEmployee\IAddEmployeeControlFactory
	 */
	private $addEmployeeControlFactory;

	/**
	 * @var \App\UserModule\Controls\Registration\IRegistrationControlFactory
	 */
	private $registrationControlFactory;


	public function __construct(
		\App\UserModule\Controls\UserListGrid\IUserListGridControlFactory $userListGridFactory,
		\App\UserModule\Controls\AddAdmin\IAddAdminControlFactory $addAdminControlFactory,
		\App\UserModule\Controls\AddEmployee\IAddEmployeeControlFactory $addEmployeeControlFactory,
		\App\UserModule\Controls\Registration\IRegistrationControlFactory $registrationControlFactory
	) {
		parent::__construct();
		$this->userListGridFactory = $userListGridFactory;
		$this->addAdminControlFactory = $addAdminControlFactory;
		$this->addEmployeeControlFactory = $addEmployeeControlFactory;
		$this->registrationControlFactory = $registrationControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionList(): void
	{
		$this->checkPermission();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionAddAdmin(): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_ADD);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionAddEmployee(): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_ADD);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionAddClient(): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_ADD);
	}


	protected function createComponentUserListGrid(): \App\UserModule\Controls\UserListGrid\UserListGridControl
	{
		return $this->userListGridFactory->create();
	}


	protected function createComponentAddAdmin(): \App\UserModule\Controls\AddAdmin\AddAdminControl
	{
		return $this->addAdminControlFactory->create();
	}


	protected function createComponentAddEmployee(): \App\UserModule\Controls\AddEmployee\AddEmployeeControl
	{
		return $this->addEmployeeControlFactory->create();
	}


	protected function createComponentAddClient(): \App\UserModule\Controls\Registration\RegistrationControl
	{
		return $this->registrationControlFactory->create(true);
	}
}
