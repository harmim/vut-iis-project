<?php

declare(strict_types=1);

namespace App\UserModule\Controls\Registration;

interface IRegistrationControlFactory
{
	function create(): \App\UserModule\Controls\Registration\RegistrationControl;
}
