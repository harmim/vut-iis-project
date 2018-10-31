<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\CloseForm;

interface ICloseFormControlFactory
{
	function create(\Nette\Database\Table\ActiveRow $record): \App\RecordModule\Controls\CloseForm\CloseFormControl;
}
