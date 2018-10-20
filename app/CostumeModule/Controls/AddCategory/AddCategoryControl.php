<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\AddCategory;

final class AddCategoryControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CostumeModule\Model\CategoryFormFactory
	 */
	private $categoryFormFactory;

	/**
	 * @var \App\CostumeModule\Model\CategoryService
	 */
	private $categoryService;


	public function __construct(
		\App\CostumeModule\Model\CategoryFormFactory $categoryFormFactory,
		\App\CostumeModule\Model\CategoryService $categoryService
	) {
		parent::__construct();
		$this->categoryFormFactory = $categoryFormFactory;
		$this->categoryService = $categoryService;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentAddForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->categoryFormFactory->createCategoryForm();

		$form->addSubmit('add', 'Vytvořit')
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
			$this->categoryService->addCategory($values);
		} catch (\App\CostumeModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		$presenter = $this->getPresenter();
		if ($presenter) {
			$presenter->flashMessage('Kategori byla úspěšně vytvořena.', 'success');
			$presenter->redirect(':Costume:Category:list');
		}
	}
}
