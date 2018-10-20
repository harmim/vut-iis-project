<?php

declare(strict_types=1);

namespace App\CostumeModule\Presenters;

final class CategoryPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \App\CostumeModule\Controls\CategoryListGrid\ICategoryListGridControlFactory
	 */
	private $categoryListGridControlFactory;

	/**
	 * @var \App\CostumeModule\Controls\AddCategory\IAddCategoryControlFactory
	 */
	private $addCategoryControlFactory;


	public function __construct(
		\App\CostumeModule\Controls\CategoryListGrid\ICategoryListGridControlFactory $categoryListGridControlFactory,
		\App\CostumeModule\Controls\AddCategory\IAddCategoryControlFactory $addCategoryControlFactory
	) {
		parent::__construct();
		$this->categoryListGridControlFactory = $categoryListGridControlFactory;
		$this->addCategoryControlFactory = $addCategoryControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionList(): void
	{
		$this->checkPermission();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionAdd(): void
	{
		$this->checkPermission();
	}


	protected function createComponentCategoryListGrid(
	): \App\CostumeModule\Controls\CategoryListGrid\CategoryListGridControl {
		return $this->categoryListGridControlFactory->create();
	}


	protected function createComponentAddCategory(): \App\CostumeModule\Controls\AddCategory\AddCategoryControl
	{
		return $this->addCategoryControlFactory->create();
	}
}
