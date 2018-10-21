<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CategoryListGrid;

final class CategoryListGridControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
	 */
	private $dataGridControlFactory;

	/**
	 * @var \App\CostumeModule\Model\CategoryService
	 */
	private $categoryService;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;


	public function __construct(
		\App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
		\App\CostumeModule\Model\CategoryService $categoryService,
		\App\CostumeModule\Model\CostumeService $costumeService
	) {
		parent::__construct();
		$this->dataGridControlFactory = $dataGridControlFactory;
		$this->categoryService = $categoryService;
		$this->costumeService = $costumeService;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
	{
		$grid = $this->dataGridControlFactory->create($this->categoryService);

		$grid->addColumnText('name', 'Název', 'nazev')
			->setFilterText();

		$grid->addColumnText('description', 'Popis', 'popis')
			->setFilterText();

		$grid->addActionDelete();

		$inlineEdit = $grid->addInlineEdit();
		$inlineEdit->onControlAdd[] = [$this, 'inlineEditOnControlAdd'];
		$inlineEdit->onSetDefaults[] = [$this, 'inlineEditOnSetDefaults'];
		$inlineEdit->onSubmit[] = [$this, 'inlineEditOnSubmit'];

		return $grid;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function handleDelete(int $id): void
	{
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_DELETE);
		}

		$category = $this->categoryService->fetchById($id);
		if (!$category) {
			$presenter->error();
			return;
		}

		if ($this->costumeService->isCategoryUsed($category)) {
			$presenter->flashMessage('Kategorie nemůže být smazána, pokud má přiřazeny nějaké kostýmy.', 'error');
		} else {
			$this->categoryService->getTable()->wherePrimary($id)->delete();
			$presenter->flashMessage('Kategorie byla smazána.', 'success');
		}

		if ($presenter->isAjax()) {
			$flashMessage = $presenter->getComponent('flashMessage');
			if ($flashMessage instanceof \Nette\Application\UI\Control) {
				$flashMessage->redrawControl();
			}

			$grid = $this->getComponent('grid');
			if ($grid instanceof \Ublaboo\DataGrid\DataGrid) {
				$grid->redrawControl();
			}
		} else {
			$presenter->redirect('this');
		}
	}


	public function inlineEditOnControlAdd(\Nette\Forms\Container $container): void
	{
		$container->addText('name')
			->setRequired();
		$container->addText('description');
	}


	public function inlineEditOnSetDefaults(
		\Nette\Forms\Container $container,
		\Nette\Database\Table\ActiveRow $row
	): void {
		$container->setDefaults([
			'name' => $row->nazev,
			'description' => $row->popis,
		]);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function inlineEditOnSubmit(int $id, \Nette\Utils\ArrayHash $values): void
	{
		$presenter = $this->getPresenter();
		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
		}

		$values->id = $id;
		$this->categoryService->editCategory($values);
	}
}
