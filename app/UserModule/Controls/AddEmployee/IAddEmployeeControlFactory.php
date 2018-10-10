<?php

declare(strict_types=1);

namespace App\UserModule\Controls\AddEmployee;

interface IAddEmployeeControlFactory
{
	function create(): \App\UserModule\Controls\AddEmployee\AddEmployeeControl;
}
