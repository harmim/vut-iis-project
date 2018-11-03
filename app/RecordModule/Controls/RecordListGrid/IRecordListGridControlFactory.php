<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\RecordListGrid;

interface IRecordListGridControlFactory
{
	function create(): \App\RecordModule\Controls\RecordListGrid\RecordListGridControl;
}
