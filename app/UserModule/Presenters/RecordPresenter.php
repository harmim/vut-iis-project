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
     * @var \App\UserModule\Controls\ClientRecordListGrid\IClientRecordListGridControlFactory
     */
    private $clientRecordListGridFactory;

    /**
     * @var \App\UserModule\Controls\CloseForm\ICloseFormControlFactory
     */
    private $closeForm;

    /**
     * @var \App\UserModule\Controls\ConfirmForm\IConfirmFormControlFactory
     */
    private $confirmForm;


    public function __construct(
        \App\UserModule\Model\RecordService $recordService,
        \App\UserModule\Controls\RecordListGrid\IRecordListGridControlFactory $recordListGridFactory,
        \App\UserModule\Controls\CloseForm\ICloseFormControlFactory $closeFormFactory,
        \App\UserModule\Controls\ConfirmForm\IConfirmFormControlFactory $confirmFormFactory,
        \App\UserModule\Controls\ClientRecordListGrid\IClientRecordListGridControlFactory $clientRecordListGridFactory
    ) {
        parent::__construct();
        $this->recordListGridFactory = $recordListGridFactory;
        $this->recordService = $recordService;
        $this->closeForm = $closeFormFactory;
        $this->confirmForm = $confirmFormFactory;
        $this->clientRecordListGridFactory = $clientRecordListGridFactory;
    }


    protected function createComponentRecordListGrid(): \App\UserModule\Controls\RecordListGrid\RecordListGridControl
    {
        return $this->recordListGridFactory->create();
    }

    protected function createComponentClientRecordListGrid(): \App\UserModule\Controls\ClientRecordListGrid\ClientRecordListGridControl
    {
        return $this->clientRecordListGridFactory->create($this->recordService, $this->getUser()->getId());
    }

    protected function createComponentCloseForm(): \App\UserModule\Controls\CloseForm\CloseFormControl
    {
        return $this->closeForm->create($this->recordService->fetchById(intval($this->params['id'])));
    }

    protected function createComponentConfirmForm(): \App\UserModule\Controls\ConfirmForm\ConfirmFormControl
    {
        return $this->confirmForm->create($this->recordService->fetchById(intval($this->params['id'])), $this->getUser()->getId());
    }



    /**
     * @throws \Nette\Application\BadRequestException
     * @throws \Nette\Application\AbortException
     */
    public function actionDetails(int $id): void
    {
        $record = $this->recordService->fetchById($id);

        $this->template->record = $record;

        if (!$this->template->record) {
            $this->error();
            return;
        }

        if ($record->kostym_id){
            $this->template->costume = $this->recordService->readCostume($record->kostym_id);
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