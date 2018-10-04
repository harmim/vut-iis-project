<?php

declare(strict_types=1);

namespace App\UserModule\Controls\Login;

interface ILoginControlFactory
{
	function create(): \App\UserModule\Controls\Login\LoginControl;
}
