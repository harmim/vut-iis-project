<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class ProfilePresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
    /**
     * @var \Nette\Database\Table\ActiveRow|null
     */
    private $user;

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

    public function __construct(\App\UserModule\Model\UserService $userService,
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
        $user = $this->userService->fetchById($id);

        if (!$user) {
            $this->error();
            return;
        }

        switch ($user->typ) {
            case \App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN:
                $this->redirect(':User:Profile:editAdmin', ['id' => $id]);
                return;

            case \App\UserModule\Model\AuthorizatorFactory::ROLE_EMPLOYEE:
                $this->redirect(':User:Profile:editEmployee', ['id' => $id]);
                return;

            case \App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT:
                $this->redirect(':User:Profile:editClient', ['id' => $id]);
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

        if ($this->user->typ !== \App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN) {
        	$this->redirect(':User:Profile:edit');
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

		if ($this->user->typ !== \App\UserModule\Model\AuthorizatorFactory::ROLE_EMPLOYEE) {
			$this->redirect(':User:Profile:edit');
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

		if ($this->user->typ !== \App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT) {
			$this->redirect(':User:Profile:edit');
			return;
		}
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
