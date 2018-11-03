<?php

declare(strict_types=1);

namespace App\SupplementModule\Model;

final class SupplementFormFactory
{
	use \Nette\SmartObject;

	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;


	public function __construct(\App\UserModule\Model\UserService $userService)
	{
		$this->userService = $userService;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	public function createSupplementForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$row = $form->addRow();
		$row->addCell()
			->addText('name', 'Název')
			->setRequired();
		$row->addCell()
			->addText('description', 'Popis');

		$row = $form->addRow();
		$row->addCell()
			->addText('price', 'Cena')
			->setType('number')
			->setRequired();
		$row->addCell()
			->addDateTime('createdDate', 'Datum výroby')
			->setAttribute('data-provide', 'datepicker')
			->setAttribute('data-date-format', 'd.m.yyyy')
			->setRequired()
			->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);

		$row = $form->addRow();
		$row->addCell()
			->addSelect('availability', 'Dostupnost', \App\CoreModule\Model\Availability::AVAILABILITIES)
			->setPrompt('Vyberte')
			->setRequired();
		$row->addCell()
			->addSelect('employee', 'Správce doplňku', $this->userService->getEmployeeEmails())
			->setPrompt('Vyberte')
			->setRequired();

		$row = $form->addRow();
		$row->addCell()
			->addUpload('image', 'Obrázek')
			->setButtonCaption('Vyberte')
			->setRequired(false)
			->addRule(\Nette\Forms\Form::IMAGE)
			->addRule(\Nette\Forms\Form::MAX_FILE_SIZE, null, 32 * \pow(1024, 2));

		$form->addProtection();

		return $form;
	}
}
