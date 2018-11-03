<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\EditSupplement;

interface IEditSupplementControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $supplement
	): \App\SupplementModule\Controls\EditSupplement\EditSupplementControl;
}
