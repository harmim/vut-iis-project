<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class ProfilePresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $editedUser;

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
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Controls\EditAdmin\IEditAdminControlFactory $editAdminControlFactory,
		\App\UserModule\Controls\EditEmployee\IEditEmployeeControlFactory $editEmployeeControlFactory,
		\App\UserModule\Controls\EditClient\IEditClientControlFactory $editClientControlFactory
	) {
		parent::__construct();
		$this->userService = $userService;
		$this->editAdminControlFactory = $editAdminControlFactory;
		$this->editEmployeeControlFactory = $editEmployeeControlFactory;
		$this->editClientControlFactory = $editClientControlFactory;
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function actionEdit(): void
	{
		$this->checkPermission();

		$id = $this->getUser()->getId();
		$this->editedUser = $this->userService->fetchById($id);

		if (!$this->editedUser) {
			$this->error();
			return;
		}
	}


	public function renderEdit(): void
	{
		$this->getTemplate()->add('editedUser', $this->editedUser);
		$this->getTemplate()->add('title', 'Můj profil');
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditAdmin(): ?\App\UserModule\Controls\EditAdmin\EditAdminControl
	{
		if (!$this->editedUser) {
			$this->error();
			return null;
		}

		return $this->editAdminControlFactory->create($this->editedUser);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditEmployee(): ?\App\UserModule\Controls\EditEmployee\EditEmployeeControl
	{
		if (!$this->editedUser) {
			$this->error();
			return null;
		}

		return $this->editEmployeeControlFactory->create($this->editedUser);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditClient(): ?\App\UserModule\Controls\EditClient\EditClientControl
	{
		if (!$this->editedUser) {
			$this->error();
			return null;
		}

		return $this->editClientControlFactory->create($this->editedUser);
	}
}
