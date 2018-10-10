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

		$grid->addColumnText('typ', 'Role')
			->setRenderer(function (\Nette\Database\Table\ActiveRow $row): string {
				return \App\UserModule\Model\UserService::ROLE_TRANSLATION_MAP[$row->typ];
			})
			->setFilterSelect(\App\UserModule\Model\UserService::ROLE_TRANSLATION_MAP);

		$grid->addColumnText('email', 'E-mail')
			->setFilterText();

		$grid->addColumnActive([$this, 'onActiveChange']);

		return $grid;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function onActiveChange(int $id, bool $active): void
	{
		$presenter = $this->getPresenter();
		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
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
