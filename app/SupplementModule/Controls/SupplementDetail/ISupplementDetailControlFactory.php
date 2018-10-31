<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\SupplementDetail;

interface ISupplementDetailControlFactory
{
	function create(
		\Nette\Database\Table\ActiveRow $supplement
	): \App\SupplementModule\Controls\SupplementDetail\SupplementDetailControl;
}
