<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CostumeDetail;

final class CostumeDetailControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $costume;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;

	/**
	 * @var \App\BorrowModule\Model\BorrowService
	 */
	private $borrowService;


	public function __construct(
		\Nette\Database\Table\ActiveRow $costume,
		\Nette\Security\User $user,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\App\BorrowModule\Model\BorrowService $borrowService
	) {
		parent::__construct();
		$this->costume = $costume;
		$this->user = $user;
		$this->costumeService = $costumeService;
		$this->borrowService = $borrowService;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('costume', $this->costume);

		$showReservation = $this->user->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT);
		$this->getTemplate()->add('showReservation', $showReservation);

		$this->getTemplate()->add(
			'isCostumeReservable',
			$showReservation ? $this->costumeService->isCostumeReservable($this->costume) : false
		);
	}


	protected function createComponentReservationForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = new \Czubehead\BootstrapForms\BootstrapForm();

		$form->addText('eventName', 'Název akce pro zapůjčení kostýmu')
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
			|| !$this->costumeService->isCostumeReservable($this->costume)
		) {
			$presenter->flashMessage('Kostým není možné rezervovat.', 'error');
			$presenter->redirect('this');
			return;
		}

		try {
			$this->borrowService->makeCostumeReservation(
				$this->costume,
				$values->eventName,
				$identity->klient_id,
				$values->borrowDate
			);
		} catch (\App\BorrowModule\Model\Exception $e) {
			$presenter->flashMessage($e->getMessage(), 'error');
			return;
		}

		$presenter->flashMessage('Kostým byl úspěšně rezervován.', 'success');
		$presenter->redirect('this');
	}
}
