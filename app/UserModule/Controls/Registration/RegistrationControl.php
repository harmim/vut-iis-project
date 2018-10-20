<?php

declare(strict_types=1);

namespace App\UserModule\Controls\Registration;

final class RegistrationControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;

	/**
	 * @var \App\UserModule\Model\UserFormFactory
	 */
	private $userFormFactory;

	/**
	 * @var bool
	 */
	private $addClientTemplate;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Model\UserFormFactory $userFormFactory,
		bool $addClientTemplate = false
	) {
		parent::__construct();
		$this->userService = $userService;
		$this->userFormFactory = $userFormFactory;
		$this->addClientTemplate = $addClientTemplate;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentRegistrationForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->userFormFactory->createClientForm();

		$form->addSubmit('register', $this->addClientTemplate ? 'Vytvořit' : 'Registrovat')
			->setAttribute('class', 'btn btn-primary btn-block');

		$form->onSuccess[] = [$this, 'onSuccessRegistrationForm'];

		return $form;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\Application\AbortException
	 */
	public function onSuccessRegistrationForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		try {
			$this->userService->registrateClient($values);
		} catch (\App\UserModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		$presenter = $this->getPresenter();
		if ($presenter) {
			if ($this->addClientTemplate) {
				$presenter->flashMessage('Klient byl úspěšně vytvořen.', 'success');
				$presenter->redirect(':User:User:list');
			} else {
				$presenter->flashMessage('Registrace proběhla úspěšně.', 'success');
				$presenter->redirect(':User:Sign:login');
			}
		}
	}
}
