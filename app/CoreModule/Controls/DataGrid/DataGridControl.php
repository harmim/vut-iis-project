<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\DataGrid;

final class DataGridControl extends \Ublaboo\DataGrid\DataGrid
{
	public function __construct(
		array $itemsPerPageList,
		array $dictionary,
		\IIS\Model\BaseService $service,
		callable $selectionCallback = null
	) {
		parent::__construct();

		$this->setDataSource($service->selectionCallback($selectionCallback));
		$this->setItemsPerPageList($itemsPerPageList);
		$this->setTranslator(new \Ublaboo\DataGrid\Localization\SimpleTranslator($dictionary));
		$this->setRememberState(false);

		$this->onColumnAdd[] = function ($key, \Ublaboo\DataGrid\Column\Column $column): void {
			$column->setSortable();
		};

		$this->addColumnNumber('id', 'ID')
			->setFormat(0, ',', '')
			->setAlign('left')
			->setFilterText();
	}


	public function getValuesTrueFalse(): array
	{
		return [
			true => 'Ano',
			false => 'Ne',
		];
	}


	public function addFilterSelect($key, $name, array $options, $column = null)
	{
		$options = ['' => 'Vše'] + $options;

		return parent::addFilterSelect($key, $name, $options, $column);
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function addActionEdit(
		?callable $condition = null,
		string $href = 'edit',
		array $params = null
	): \Ublaboo\DataGrid\Column\Action {
		$action = $this->addAction('edit', '', $href, $params);
		$action->setTitle('Editovat')
			->setClass('btn btn-xs btn-default')
			->setIcon('pencil');

		if ($condition) {
			$this->allowRowsAction('edit', function ($item) use ($condition): bool {
				return $condition($item);
			});
		}

		return $action;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function addActionDelete(
		?callable $condition = null,
		string $href = 'delete!',
		array $params = null
	): \Ublaboo\DataGrid\Column\Action {
		$action = $this->addAction('delete', '', $href, $params);
		$action->setTitle('Smazat')
			->setClass('btn btn-xs btn-danger ajax')
			->setConfirm('Opravdu chcete provést tuto akci?')
			->setIcon('trash');

		if ($condition) {
			$this->allowRowsAction('delete', function ($item) use ($condition): bool {
				return $condition($item);
			});
		}

		return $action;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 */
	public function addColumnActive(
		callable $handleOnChange,
		bool $setFilter = true
	): \Ublaboo\DataGrid\Column\ColumnStatus {
		$column = $this->addColumnStatus('active', 'Aktivní', 'aktivni')
			->setAlign('center')
			->addOption(true, 'Ano')
				->setClass('btn-success')
				->setIcon('check')
				->endOption()
			->addOption(false, 'Ne')
				->setClass('btn-danger')
				->setIcon('times')
				->endOption();

		$column->onChange[] = $handleOnChange;

		if ($setFilter) {
			$column->setFilterSelect($this->getValuesTrueFalse());
		}

		return $column;
	}
}
