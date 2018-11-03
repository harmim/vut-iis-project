<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\ConfirmForm;

final class ConfirmFormControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\RecordModule\Model\RecordService
	 */
	private $recordService;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $record;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(
		\App\RecordModule\Model\RecordService $recordService,
		\Nette\Database\Table\ActiveRow $record,
		\Nette\Security\User $user
	) {
		parent::__construct();
		$this->recordService = $recordService;
		$this->record = $record;
		$this->user = $user;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentConfirmForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addSubmit('confirm', 'Zprostředkovat výpůjčku')
			->setAttribute('class', 'btn btn-primary');

		$form->addProtection();

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
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
		}

		$this->recordService->confirmReservation($this->record, $this->user->getId());

		$presenter->flashMessage('Výpůjčka byla Zprostředkována.', 'success');
		$presenter->redirect('this');
	}
}
