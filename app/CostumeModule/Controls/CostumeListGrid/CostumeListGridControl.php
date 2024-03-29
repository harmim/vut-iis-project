<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CostumeListGrid;

final class CostumeListGridControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
	 */
	private $dataGridControlFactory;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \App\CostumeModule\Model\CategoryService
	 */
	private $categoryService;


	public function __construct(
		\App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\Nette\Security\User $user,
		\App\CostumeModule\Model\CategoryService $categoryService
	) {
		parent::__construct();
		$this->dataGridControlFactory = $dataGridControlFactory;
		$this->costumeService = $costumeService;
		$this->user = $user;
		$this->categoryService = $categoryService;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
	{
		$grid = $this->dataGridControlFactory->create($this->costumeService, [$this, 'defaultFilter']);

		$grid->addColumnText('description', 'Popis', 'popis')
			->setFilterText();

		$grid->addColumnNumber('price', 'Cena', 'cena')
			->setFilterRange();

		$grid->addColumnText('size', 'Velikost', 'velikost')
			->setFilterText();

		$grid->addColumnText('availability', 'Dostupnost', 'dostupnost')
			->setFilterSelect(\App\CoreModule\Model\Availability::AVAILABILITIES);

		$grid->addColumnText('category', 'Kategori', 'kategorie_id')
			->setRenderer([$this, 'renderCategory'])
			->setFilterSelect($this->categoryService->fetchPairs('id', 'nazev'));

		if ($this->user->isAllowed('costume.costume', \App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT)) {
			$grid->addColumnActive([$this, 'onActiveChange']);
			$grid->addActionEdit(':Costume:Costume:edit');
		}

		$actionDetail = $grid->addAction('detail', '', ':Costume:Costume:default');
		$actionDetail->setTitle('Detail')
			->setClass('btn btn-xs btn-primary')
			->setIcon('eye');

		return $grid;
	}


	public function defaultFilter(\Nette\Database\Table\Selection $selection)
	{
		if (!$this->user->isAllowed('costume.costume', \App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT)) {
			$selection->where('aktivni', true);
		}
	}


	public function renderCategory(\Nette\Database\Table\ActiveRow $row): string
	{
		return (string) $row->kategorie->nazev;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function onActiveChange(int $id, bool $active): void
	{
		$presenter = $this->getPresenter();
		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_DELETE);
		}

		$this->costumeService->changeActive($id, $active);

		if ($presenter) {
			if ($presenter->isAjax()) {
				$grid = $this->getComponent('grid');
				if ($grid instanceof \Ublaboo\DataGrid\DataGrid) {
					$grid->redrawItem($id);
				}
			} else {
				$presenter->redirect('this');
			}
		}
	}
}
