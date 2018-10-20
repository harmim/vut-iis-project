<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CostumeListGrid;

interface ICostumeListGridControlFactory
{
	function create(): \App\CostumeModule\Controls\CostumeListGrid\CostumeListGridControl;
}
