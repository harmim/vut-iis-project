<?php

declare(strict_types=1);

namespace App\CostumeModule\Model;

final class CategoryFormFactory
{
	use \Nette\SmartObject;

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function createCategoryForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$row = $form->addRow();
		$row->addCell()
			->addText('name', 'Název')
			->setRequired();
		$row->addCell()
			->addText('description', 'Popis');

		$form->addProtection();

		return $form;
	}
}
