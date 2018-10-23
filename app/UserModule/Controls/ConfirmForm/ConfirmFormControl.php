<?php

declare(strict_types=1);

namespace App\UserModule\Controls\ConfirmForm;


final class ConfirmFormControl extends \IIS\Application\UI\BaseControl
{
    /**
     * @var \Nette\Database\Table\ActiveRow
     */
    private $record;

    /**
     * @var \App\UserModule\Model\RecordService
     */
    private $recordService;

    private $userId;

    public function __construct(
        \App\UserModule\Model\RecordService $recordService,
        \Nette\Database\Table\ActiveRow $record,
        int $userId
    ) {
        parent::__construct();
        $this->recordService = $recordService;
        $this->record = $record;
        $this->userId = $userId;
    }


    /**
     * @throws \Nette\InvalidArgumentException
     */
    protected function createComponentConfirmForm(): \Czubehead\BootstrapForms\BootstrapForm
    {
        $form = new \Czubehead\BootstrapForms\BootstrapForm();

        $form->addHidden('record_id', $this->record->id);

        $form->addHidden('employee_id', $this->userId);


        $form->addSubmit('add', 'Zprostředkovat výpůjčku.')
            ->setAttribute('class', 'btn btn-primary btn-block');

        $form->onSuccess[] = [$this, 'onSuccessConfirmForm'];

        return $form;
    }


    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\InvalidArgumentException
     */
    public function onSuccessConfirmForm(
        \Czubehead\BootstrapForms\BootstrapForm $form,
        \Nette\Utils\ArrayHash $values
    ): void {
        try {
            $this->recordService->confirmReservation($values);
        } catch (\App\UserModule\Model\Exception $e) {
            $form->addError($e->getMessage());
            return;
        }

        $presenter = $this->getPresenter();
        if ($presenter) {
            $presenter->flashMessage('Výpůjčka byla Zprostředkována.', 'success');
            $presenter->redirect('this');
        }
    }
}
