<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\SupplementListGrid;

final class SupplementListGridControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
	 */
	private $dataGridControlFactory;

	/**
	 * @var \App\SupplementModule\Model\SupplementService
	 */
	private $supplementService;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $costume;


	public function __construct(
		\App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
		\App\SupplementModule\Model\SupplementService $supplementService,
		\Nette\Security\User $user,
		\Nette\Database\Table\ActiveRow $costume
	) {
		parent::__construct();
		$this->dataGridControlFactory = $dataGridControlFactory;
		$this->supplementService = $supplementService;
		$this->user = $user;
		$this->costume = $costume;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
	{
		$grid = $this->dataGridControlFactory->create($this->supplementService, [$this, 'defaultFilter']);

		$grid->addColumnText('name', 'Název', 'nazev')
			->setFilterText();

		$grid->addColumnNumber('price', 'Cena', 'cena')
			->setFilterRange();

		$grid->addColumnText('availability', 'Dostupnost', 'dostupnost')
			->setFilterSelect(\App\CoreModule\Model\Availability::AVAILABILITIES);

		if ($this->user->isAllowed('supplement.supplement', \App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT)) {
			$grid->addColumnActive([$this, 'onActiveChange']);
			$grid->addActionEdit(':Supplement:Supplement:edit');
		}

		$actionDetail = $grid->addAction('detail', '', ':Supplement:Supplement:default');
		$actionDetail->setTitle('Detail')
			->setClass('btn btn-xs btn-primary')
			->setIcon('eye');

		return $grid;
	}


	public function defaultFilter(\Nette\Database\Table\Selection $selection): void
	{
		$selection->where('kostym_id', $this->costume->id);

		if (!$this->user->isAllowed('supplement.supplement', \App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT)) {
			$selection->where('aktivni', true);
		}
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

		$this->supplementService->changeActive($id, $active);

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
