<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\AddSupplement;

interface IAddSupplementControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $costume
	): \App\SupplementModule\Controls\AddSupplement\AddSupplementControl;
}
