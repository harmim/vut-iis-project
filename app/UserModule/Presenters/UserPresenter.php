<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class UserPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $user;

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

	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;

	/**
	 * @var \App\UserModule\Controls\EditAdmin\IEditAdminControlFactory
	 */
	private $editAdminControlFactory;

	/**
	 * @var \App\UserModule\Controls\EditEmployee\IEditEmployeeControlFactory
	 */
	private $editEmployeeControlFactory;

	/**
	 * @var \App\UserModule\Controls\EditClient\IEditClientControlFactory
	 */
	private $editClientControlFactory;


	public function __construct(
		\App\UserModule\Controls\UserListGrid\IUserListGridControlFactory $userListGridFactory,
		\App\UserModule\Controls\AddAdmin\IAddAdminControlFactory $addAdminControlFactory,
		\App\UserModule\Controls\AddEmployee\IAddEmployeeControlFactory $addEmployeeControlFactory,
		\App\UserModule\Controls\Registration\IRegistrationControlFactory $registrationControlFactory,
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Controls\EditAdmin\IEditAdminControlFactory $editAdminControlFactory,
		\App\UserModule\Controls\EditEmployee\IEditEmployeeControlFactory $editEmployeeControlFactory,
		\App\UserModule\Controls\EditClient\IEditClientControlFactory $editClientControlFactory
	) {
		parent::__construct();
		$this->userListGridFactory = $userListGridFactory;
		$this->addAdminControlFactory = $addAdminControlFactory;
		$this->addEmployeeControlFactory = $addEmployeeControlFactory;
		$this->registrationControlFactory = $registrationControlFactory;
		$this->userService = $userService;
		$this->editAdminControlFactory = $editAdminControlFactory;
		$this->editEmployeeControlFactory = $editEmployeeControlFactory;
		$this->editClientControlFactory = $editClientControlFactory;
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


	/**
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function actionEdit(int $id): void
	{
		$this->checkPermission();

		$user = $this->userService->fetchById($id);
		if (!$user) {
			$this->error();
			return;
		}

		switch ($user->typ) {
			case \App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN:
				$this->redirect(':User:User:editAdmin', ['id' => $id]);
				return;

			case \App\UserModule\Model\AuthorizatorFactory::ROLE_EMPLOYEE:
				$this->redirect(':User:User:editEmployee', ['id' => $id]);
				return;

			case \App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT:
				$this->redirect(':User:User:editClient', ['id' => $id]);
				return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionEditAdmin(int $id): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);

		$this->user = $this->userService->fetchById($id);
		if (!$this->user) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionEditEmployee(int $id): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);

		$this->user = $this->userService->fetchById($id);
		if (!$this->user) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionEditClient(int $id): void
	{
		$this->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);

		$this->user = $this->userService->fetchById($id);
		if (!$this->user) {
			$this->error();
			return;
		}
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


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditAdmin(): ?\App\UserModule\Controls\EditAdmin\EditAdminControl
	{
		if (!$this->user) {
			$this->error();
			return null;
		}

		return $this->editAdminControlFactory->create($this->user);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditEmployee(): ?\App\UserModule\Controls\EditEmployee\EditEmployeeControl
	{
		if (!$this->user) {
			$this->error();
			return null;
		}

		return $this->editEmployeeControlFactory->create($this->user);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditClient(): ?\App\UserModule\Controls\EditClient\EditClientControl
	{
		if (!$this->user) {
			$this->error();
			return null;
		}

		return $this->editClientControlFactory->create($this->user);
	}
}
