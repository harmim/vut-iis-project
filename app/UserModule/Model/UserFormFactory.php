<?php

declare(strict_types=1);

namespace App\UserModule\Model;

final class UserFormFactory
{
	use \Nette\SmartObject;

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function createAdminForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addText('email', 'E-mail')
			->setType('email')
			->setRequired()
			->addRule(\Nette\Forms\Form::EMAIL);

		$row = $form->addRow();
		$cell = $row->addCell();
		$password = $cell->addPassword('password', 'Heslo')
			->setRequired()
			->addRule(\Nette\Forms\Form::MIN_LENGTH, null, 6);
		$row->addCell()
			->addPassword('passwordConfirmation', 'Potvrzení hesla')
			->addConditionOn($password, \Nette\Forms\Form::FILLED)
				->setRequired()
				->addRule(\Nette\Forms\Form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

		$form->addProtection();

		return $form;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function createEmployeeForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->createAdminForm();

		$row = $form->addRow();
		$row->addCell()
			->addText('firstName', 'Jméno')
			->setRequired();
		$row->addCell()
			->addText('lastName', 'Příjmení')
			->setRequired();

		$row = $form->addRow();
		$row->addCell()
			->addDateTime('bornDate', 'Datum narozeí')
			->setAttribute('data-provide', 'datepicker')
			->setAttribute('data-date-format', 'd.m.yyyy')
			->setRequired()
			->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);
		$row->addCell()
			->addText('phone', 'Telefonní číslo')
			->setRequired();

		return $form;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function createClientForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->createAdminForm();

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

		$useCompany = $form->addCheckbox('useCompany', 'Právnická osoba');
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

		return $form;
	}
}
