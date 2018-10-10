<?php

declare(strict_types=1);

namespace App\UserModule\Controls\UserListGrid;

interface IUserListGridControlFactory
{
	function create(): \App\UserModule\Controls\UserListGrid\UserListGridControl;
}
