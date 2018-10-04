<?php

declare(strict_types=1);

namespace App\UserModule\Controls\Login;

final class LoginControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @persistent
	 * @var string
	 */
	public $backLink = '';

	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(\Nette\Security\User $user)
	{
		parent::__construct();
		$this->user = $user;
	}


	protected function createComponentLoginForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addText('email', 'E-mail')
			->setType('email')
			->setRequired()
			->addRule(\Nette\Forms\Form::EMAIL);

		$form->addPassword('password', 'Heslo')
			->setRequired();

		$form->addCheckbox('remember', 'Zůstat přihlášen');

		$form->addSubmit('login', 'Přihlásit se')
			->setAttribute('class', 'btn btn-primary btn-block');

		$form->onSuccess[] = [$this, 'onSuccessLoginForm'];

		return $form;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function onSuccessLoginForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		try {
			$this->user->setExpiration(
				$values->remember ? '14 days' : '30 minutes',
				\Nette\Security\IUserStorage::CLEAR_IDENTITY
			);
			$this->user->login($values->email, $values->password);
		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}

		$this->getPresenter()->flashMessage('Byli jste úspěšně přihlášení.', 'success');
		if ($this->backLink) {
			$this->getPresenter()->restoreRequest($this->backLink);
		}
		$this->getPresenter()->redirect(':Core:Homepage:default');
	}
}
