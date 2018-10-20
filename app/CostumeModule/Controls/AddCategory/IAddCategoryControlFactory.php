<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\AddCategory;

interface IAddCategoryControlFactory
{
	function create(): \App\CostumeModule\Controls\AddCategory\AddCategoryControl;
}
