<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\CloseForm;

final class CloseFormControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\RecordModule\Model\RecordService
	 */
	private $recordService;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $record;


	public function __construct(
		\App\RecordModule\Model\RecordService $recordService,
		\Nette\Database\Table\ActiveRow $record
	) {
		parent::__construct();
		$this->recordService = $recordService;
		$this->record = $record;
	}


	protected function createComponentCloseForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addDateTime('returnDate', 'Datum vrácení')
			->setAttribute('data-provide', 'datepicker')
			->setAttribute('data-date-format', 'd.m.yyyy')
			->setRequired()
			->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);

		$form->addSubmit('close', 'Uzavřít výpůjčku')
			->setAttribute('class', 'btn btn-primary');

		$form->addProtection();

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
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
		}

		$this->recordService->closeRecord($this->record, $values->returnDate);

		$presenter->flashMessage('Výpůjčka byla uzavřena.', 'success');
		$presenter->redirect('this');
	}
}
