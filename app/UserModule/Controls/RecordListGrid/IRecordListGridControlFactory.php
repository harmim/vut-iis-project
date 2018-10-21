<?php

declare(strict_types=1);

namespace App\UserModule\Controls\RecordListGrid;

interface IRecordListGridControlFactory
{
    function create(): \App\UserModule\Controls\RecordListGrid\RecordListGridControl;
}
