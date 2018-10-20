<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\EditCostume;

interface IEditCostumeControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $costume
	): \App\CostumeModule\Controls\EditCostume\EditCostumeControl;
}
