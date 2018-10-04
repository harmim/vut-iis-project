<?php

declare(strict_types=1);

namespace App\UserModule\Controls\Registration;

final class RegistrationControl extends \IIS\Application\UI\BaseControl
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
	protected function createComponentRegistrationForm(): \Czubehead\BootstrapForms\BootstrapForm
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

		$row = $form->addRow();
		$row->addCell()
			->addText('firstName', 'Jméno')
			->setRequired();
		$row->addCell()
			->addText('lastName', 'Příjmení')
			->setRequired();

		$form->addDateTime('bornDate', 'Datum narozeí')
			->setAttribute('data-provide', 'datepicker')
			->setAttribute('data-date-format', 'd.m.yyyy')
			->setRequired()
			->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);

		$row = $form->addRow();
		$row->addCell()
			->addText('phone', 'Telefonní číslo')
			->setRequired();
		$row->addCell()
			->addText('address', 'Adresa')
			->setRequired();

		$useCompany = $form->addCheckbox('useCompany', 'Registrovat se jako právnická osoba');
		$row = $form->addRow();
		$row->addCell()
			->addText('ico', 'IČO')
			->addConditionOn($useCompany, \Nette\Forms\Form::EQUAL, true)
				->setRequired();
		$row->addCell()
			->addText('dic', 'DIČ')
			->addConditionOn($useCompany, \Nette\Forms\Form::EQUAL, true)
				->setRequired();
		$form->addText('companyAddress', 'Fakturační adresa')
			->addConditionOn($useCompany, \Nette\Forms\Form::EQUAL, true)
				->setRequired();

		$form->addSubmit('register', 'Registrovat')
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
			$presenter->flashMessage('Registrace proběhla úspěšně.', 'success');
			$presenter->redirect(':User:Sign:login');
		}
	}
}
