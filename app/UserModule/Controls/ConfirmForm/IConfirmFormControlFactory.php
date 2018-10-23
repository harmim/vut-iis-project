<?php

declare(strict_types=1);

namespace App\UserModule\Controls\ConfirmForm;

interface IConfirmFormControlFactory
{
    function create(\Nette\Database\Table\ActiveRow $record,int $userId): \App\UserModule\Controls\ConfirmForm\ConfirmFormControl;
}