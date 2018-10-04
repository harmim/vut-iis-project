<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\FlashMessage;

interface IFlashMessageControlFactory
{
	function create(): \App\CoreModule\Controls\FlashMessage\FlashMessageControl;
}
