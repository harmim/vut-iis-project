<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class RecordPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
    /**
     * @var \App\UserModule\Model\RecordService
     */
    private $recordService;

    /**
     * @var \App\UserModule\Controls\RecordListGrid\IRecordListGridControlFactory
     */
    private $recordListGridFactory;

    /**
     * @var \App\UserModule\Controls\CloseForm\ICloseFormControlFactory
     */
    private $closeForm;


    public function __construct(
        \App\UserModule\Model\RecordService $recordService,
        \App\UserModule\Controls\RecordListGrid\IRecordListGridControlFactory $recordListGridFactory,
        \App\UserModule\Controls\CloseForm\ICloseFormControlFactory $closeFormFactory
    ) {
        parent::__construct();
        $this->recordListGridFactory = $recordListGridFactory;
        $this->recordService = $recordService;
        $this->closeForm = $closeFormFactory;
    }


    protected function createComponentRecordListGrid(): \App\UserModule\Controls\RecordListGrid\RecordListGridControl
    {
        return $this->recordListGridFactory->create();
    }

    protected function createComponentCloseForm(): \App\UserModule\Controls\CloseForm\CloseFormControl
    {
        return $this->closeForm->create($this->recordService->fetchById(intval($this->params['id'])));
    }



    /**
     * @throws \Nette\Application\BadRequestException
     * @throws \Nette\Application\AbortException
     */
    public function actionDetails(int $id): void
    {
//      $this->checkPermission();


        $record = $this->recordService->fetchById($id);

        $this->template->record = $record;

        if (!$this->template->record) {
            $this->error();
            return;
        }

        if ($record->kostym_id){
            $this->template->kostym = $this->recordService->readCostume($record->kostym_id);
        }

        if ($record->doplnek_id){
            $this->template->doplnek = $this->recordService->readAcessory($record->doplnek_id);
        }

        if ($record->klient_id){
            $this->template->klient = $this->recordService->readClient($record->klient_id);
        }

        if ($record->zamestnanec_id){
            $this->template->zamestnanec = $this->recordService->readEmployee($record->zamestnanec_id);
        }
    }
}