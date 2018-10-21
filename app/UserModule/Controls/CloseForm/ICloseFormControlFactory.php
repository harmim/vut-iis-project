<?php

declare(strict_types=1);

namespace App\UserModule\Controls\CloseForm;

interface ICloseFormControlFactory
{
    function create(\Nette\Database\Table\ActiveRow $record): \App\UserModule\Controls\CloseForm\CloseFormControl;
}
