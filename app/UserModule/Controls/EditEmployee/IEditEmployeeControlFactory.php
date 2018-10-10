<?php

declare(strict_types=1);

namespace App\UserModule\Controls\EditEmployee;

interface IEditEmployeeControlFactory
{
	function create(\Nette\Database\Table\ActiveRow $user): \App\UserModule\Controls\EditEmployee\EditEmployeeControl;
}
