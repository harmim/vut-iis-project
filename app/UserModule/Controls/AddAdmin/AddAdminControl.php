<?php

declare(strict_types=1);

namespace App\UserModule\Controls\AddAdmin;

final class AddAdminControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;


	public function __construct(\App\UserModule\Model\UserService $userService)
	{
		parent::__construct();
		$this->userService = $userService;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentAddForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addText('email', 'E-mail')
			->setType('email')
			->setRequired()
			->addRule(\Nette\Forms\Form::EMAIL);

		$row = $form->addRow();
		$row->addCell()
			->addPassword('password', 'Heslo')
			->setRequired()
			->addRule(\Nette\Forms\Form::MIN_LENGTH, null, 6);
		$row->addCell()
			->addPassword('passwordConfirmation', 'Potvrzení hesla')
			->setRequired()
			->addRule(\Nette\Forms\Form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

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
			$this->userService->addAdmin($values);
		} catch (\App\UserModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		$presenter = $this->getPresenter();
		if ($presenter) {
			$presenter->flashMessage('Administrátor byl úspěšně přidán.', 'success');
			$presenter->redirect(':User:User:list');
		}
	}
}
