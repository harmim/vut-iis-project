<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\Header;

interface IHeaderControlFactory
{
	function create(): \App\CoreModule\Controls\Header\HeaderControl;
}
