<?php

declare(strict_types=1);

namespace App\UserModule\Controls\EditAdmin;

interface IEditAdminControlFactory
{
	function create(\Nette\Database\Table\ActiveRow $user): \App\UserModule\Controls\EditAdmin\EditAdminControl;
}
