<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\AddCostume;

interface IAddCostumeControlFactory
{
	function create(): \App\CostumeModule\Controls\AddCostume\AddCostumeControl;
}
