<?php

declare(strict_types=1);

namespace App\UserModule\Controls\EditClient;

interface IEditClientControlFactory
{
	function create(\Nette\Database\Table\ActiveRow $user): \App\UserModule\Controls\EditClient\EditClientControl;
}
