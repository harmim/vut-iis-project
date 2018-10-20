<?php

declare(strict_types=1);

namespace App\CostumeModule\Model;

final class CostumeFormFactory
{
	use \Nette\SmartObject;

	/**
	 * @var \App\CostumeModule\Model\CategoryService
	 */
	private $categoryService;


	public function __construct(\App\CostumeModule\Model\CategoryService $categoryService)
	{
		$this->categoryService = $categoryService;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	public function createCostumeForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$row = $form->addRow();
		$row->addCell()
			->addText('manufacturer', 'Výrobce')
			->setRequired();
		$row->addCell()
			->addText('material', 'Materiál')
			->setRequired();

		$form->addText('description', 'Popis')
			->setRequired();

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
			->addText('wear', 'Opotřebení');
		$row->addCell()
			->addText('size', 'Velikost')
			->setRequired();

		$row = $form->addRow();
		$row->addCell()
			->addText('color', 'Barva')
			->setRequired();
		$row->addCell()
			->addSelect('availability', 'Dostupnost', \App\CoreModule\Model\Availability::AVAILABILITIES)
			->setPrompt('Vyberte')
			->setRequired();

		$row = $form->addRow();
		$row->addCell()
			->addSelect('category', 'Kategorie', $this->categoryService->fetchPairs('id', 'nazev'))
			->setPrompt('Vyberte')
			->setRequired();
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
