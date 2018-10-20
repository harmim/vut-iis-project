<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CostumeDetail;

interface ICostumeDetailControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $costume
	): \App\CostumeModule\Controls\CostumeDetail\CostumeDetailControl;
}
