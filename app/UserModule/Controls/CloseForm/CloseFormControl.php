<?php

declare(strict_types=1);

namespace App\UserModule\Controls\CloseForm;


final class CloseFormControl extends \IIS\Application\UI\BaseControl
{
    /**
     * @var \Nette\Database\Table\ActiveRow
     */
    private $record;

    /**
     * @var \App\UserModule\Model\RecordService
     */
    private $recordService;


    public function __construct(
        \App\UserModule\Model\RecordService $recordService,
        \Nette\Database\Table\ActiveRow $record
    ) {
        parent::__construct();
        $this->recordService = $recordService;
        $this->record = $record;
    }


    /**
     * @throws \Nette\InvalidArgumentException
     */
    protected function createComponentCloseForm(): \Czubehead\BootstrapForms\BootstrapForm
    {
        $form = new \Czubehead\BootstrapForms\BootstrapForm();

        $form->addHidden('id', $this->record->id);

        $form->addDateTime('cas_vraceni', 'Čas vrácení:')
            ->setAttribute('data-provide', 'datepicker')
            ->setAttribute('data-date-format', 'd.m.yyyy')
            ->setRequired()
            ->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);

        $form->addSubmit('add', 'Uzavřít výpůjčku')
            ->setAttribute('class', 'btn btn-primary btn-block');

        $form->onSuccess[] = [$this, 'onSuccessCloseForm'];

        return $form;
    }


    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\InvalidArgumentException
     */
    public function onSuccessCloseForm(
        \Czubehead\BootstrapForms\BootstrapForm $form,
        \Nette\Utils\ArrayHash $values
    ): void {
        try {
            $this->recordService->closeRecord($values);
        } catch (\App\UserModule\Model\Exception $e) {
            $form->addError($e->getMessage());
            return;
        }

        $presenter = $this->getPresenter();
        if ($presenter) {
            $presenter->flashMessage('Výpůjčka byla uzavřena', 'success');
            $presenter->redirect('this');
        }
    }
}
