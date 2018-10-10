<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\DataGrid;

interface IDataGridControlFactory
{
	function create(
		\IIS\Model\BaseService $service,
		callable $selectionCallback = null
	): \App\CoreModule\Controls\DataGrid\DataGridControl;
}
