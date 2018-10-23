<?php

declare(strict_types=1);

namespace App\UserModule\Controls\ClientRecordListGrid;

interface IClientRecordListGridControlFactory{
    function create(\App\UserModule\Model\RecordService $recordService,int $userId): \App\UserModule\Controls\ClientRecordListGrid\ClientRecordListGridControl;
}