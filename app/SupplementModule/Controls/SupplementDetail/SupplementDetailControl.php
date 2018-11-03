<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\SupplementDetail;

final class SupplementDetailControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $supplement;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \App\SupplementModule\Model\SupplementService
	 */
	private $supplementService;

	/**
	 * @var \App\RecordModule\Model\RecordService
	 */
	private $recordService;


	public function __construct(
		\Nette\Database\Table\ActiveRow $supplement,
		\Nette\Security\User $user,
		\App\SupplementModule\Model\SupplementService $supplementService,
		\App\RecordModule\Model\RecordService $recordService
	) {
		parent::__construct();
		$this->supplement = $supplement;
		$this->user = $user;
		$this->supplementService = $supplementService;
		$this->recordService = $recordService;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('supplement', $this->supplement);

		$showReservation = $this->user->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT);
		$this->getTemplate()->add('showReservation', $showReservation);

		$this->getTemplate()->add(
			'isSupplementReservable',
			$showReservation ? $this->supplementService->isSupplementReservable($this->supplement) : false
		);
	}


	protected function createComponentReservationForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addText('eventName', 'Název akce pro zapůjčení doplňku')
			->setRequired();

		$form->addDateTime('borrowDate', 'Datum zapůjčení')
			->setAttribute('data-provide', 'datepicker')
			->setAttribute('data-date-format', 'd.m.yyyy')
			->setRequired()
			->setFormat(\Czubehead\BootstrapForms\Enums\DateTimeFormat::D_DMY_DOTS_NO_LEAD);

		$form->addSubmit('makeReservation', 'Rezervovat')
			->setAttribute('class', 'btn btn-success');

		$form->addProtection();

		$form->onSuccess[] = [$this, 'onSuccessReservationForm'];

		return $form;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function onSuccessReservationForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		$identity = $this->user->getIdentity();
		if (
			!$identity instanceof \Nette\Security\Identity
			|| !$this->user->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT)
			|| !$this->supplementService->isSupplementReservable($this->supplement)
		) {
			$presenter->flashMessage('Doplněk není možné rezervovat.', 'error');
			$presenter->redirect('this');
			return;
		}

		try {
			$this->recordService->makeSupplementReservation(
				$this->supplement,
				$values->eventName,
				$identity->klient_id,
				$values->borrowDate
			);
		} catch (\App\RecordModule\Model\Exception $e) {
			$presenter->flashMessage($e->getMessage(), 'error');
			return;
		}

		$presenter->flashMessage('Doplněk byl úspěšně rezervován.', 'success');
		$presenter->redirect('this');
	}
}
