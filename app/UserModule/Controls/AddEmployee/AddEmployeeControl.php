<?php

declare(strict_types=1);

namespace App\UserModule\Controls\AddEmployee;

final class AddEmployeeControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;

	/**
	 * @var \App\UserModule\Model\UserFormFactory
	 */
	private $userFormFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Model\UserFormFactory $userFormFactory
	) {
		parent::__construct();
		$this->userService = $userService;
		$this->userFormFactory = $userFormFactory;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentAddForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->userFormFactory->createEmployeeForm();

		$form->addSubmit('add', 'Přidat')
			->setAttribute('class', 'btn btn-primary btn-block');

		$form->onSuccess[] = [$this, 'onSuccessAddForm'];

		return $form;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function onSuccessAddForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		try {
			$this->userService->addEmployee($values);
		} catch (\App\UserModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		$presenter = $this->getPresenter();
		if ($presenter) {
			$presenter->flashMessage('Zaměstnanec byl úspěšně přidán.', 'success');
			$presenter->redirect(':User:User:list');
		}
	}
}
