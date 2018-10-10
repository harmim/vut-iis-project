<?php

declare(strict_types=1);

namespace App\UserModule\Controls\AddAdmin;

interface IAddAdminControlFactory
{
	function create(): \App\UserModule\Controls\AddAdmin\AddAdminControl;
}
