<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\ConfirmForm;

interface IConfirmFormControlFactory
{
	function create(\Nette\Database\Table\ActiveRow $record): \App\RecordModule\Controls\ConfirmForm\ConfirmFormControl;
}
