<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CategoryListGrid;

interface ICategoryListGridControlFactory
{
	function create(): \App\CostumeModule\Controls\CategoryListGrid\CategoryListGridControl;
}
