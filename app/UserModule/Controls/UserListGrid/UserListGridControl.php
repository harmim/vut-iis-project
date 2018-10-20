<?php

declare(strict_types=1);

namespace App\UserModule\Controls\UserListGrid;

final class UserListGridControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
	 */
	private $dataGridControlFactory;

	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;


	public function __construct(
		\App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
		\App\UserModule\Model\UserService $userService
	) {
		parent::__construct();
		$this->dataGridControlFactory = $dataGridControlFactory;
		$this->userService = $userService;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 */
	protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
	{
		$grid = $this->dataGridControlFactory->create($this->userService);

		$grid->addColumnText('type', 'Role', 'typ')
			->setRenderer([$this, 'renderRole'])
			->setFilterSelect(\App\UserModule\Model\UserService::ROLE_TRANSLATION_MAP);

		$grid->addColumnText('email', 'E-mail', 'email')
			->setFilterText();

		$grid->addColumnActive([$this, 'onActiveChange']);

		$grid->addActionEdit();

		return $grid;
	}


	public function renderRole(\Nette\Database\Table\ActiveRow $row): string
	{
		return \App\UserModule\Model\UserService::ROLE_TRANSLATION_MAP[$row->typ];
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

		$this->userService->changeActive($id, $active);

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
