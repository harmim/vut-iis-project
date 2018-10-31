<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\SupplementListGrid;

interface ISupplementListGridControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $costume
	): \App\SupplementModule\Controls\SupplementListGrid\SupplementListGridControl;
}
