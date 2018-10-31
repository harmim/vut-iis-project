<?php

declare(strict_types=1);

namespace App\RecordModule\Presenters;

final class RecordPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $record;

	/**
	 * @var \App\RecordModule\Model\RecordService
	 */
	private $recordService;

	/**
	 * @var \App\RecordModule\Controls\RecordListGrid\IRecordListGridControlFactory
	 */
	private $recordListGridFactory;

	/**
	 * @var \App\RecordModule\Controls\CloseForm\ICloseFormControlFactory
	 */
	private $closeFormFactory;

	/**
	 * @var \App\RecordModule\Controls\ConfirmForm\IConfirmFormControlFactory
	 */
	private $confirmFormFactory;


	public function __construct(
		\App\RecordModule\Model\RecordService $recordService,
		\App\RecordModule\Controls\RecordListGrid\IRecordListGridControlFactory $recordListGridFactory,
		\App\RecordModule\Controls\CloseForm\ICloseFormControlFactory $closeFormFactory,
		\App\RecordModule\Controls\ConfirmForm\IConfirmFormControlFactory $confirmFormFactory
	) {
		parent::__construct();
		$this->recordService = $recordService;
		$this->recordListGridFactory = $recordListGridFactory;
		$this->closeFormFactory = $closeFormFactory;
		$this->confirmFormFactory = $confirmFormFactory;
	}


	protected function createComponentRecordListGrid(): \App\RecordModule\Controls\RecordListGrid\RecordListGridControl
	{
		return $this->recordListGridFactory->create();
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentCloseForm(): ?\App\RecordModule\Controls\CloseForm\CloseFormControl
	{
		if (!$this->record) {
			$this->error();
			return null;
		}

		return $this->closeFormFactory->create($this->record);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentConfirmForm(): ?\App\RecordModule\Controls\ConfirmForm\ConfirmFormControl
	{
		if (!$this->record) {
			$this->error();
			return null;
		}

		return $this->confirmFormFactory->create($this->record);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionList(): void
	{
		$this->checkPermission();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDefault(int $id): void
	{
		$this->checkPermission();

		$this->record = $this->recordService->fetchById($id);
		if (!$this->record) {
			$this->error();
			return;
		}

		if ($this->getUser()->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT)) {
			$identity = $this->getUser()->getIdentity();
			if ($identity instanceof \Nette\Security\Identity && $this->record->klient_id !== $identity->klient_id) {
				$this->error();
				return;
			}
		}
	}


	/**
	 * @throws \Nette\MemberAccessException
	 */
	public function renderDefault(): void
	{
		if (!$this->record) {
			return;
		}

		$this->getTemplate()->add('record', $this->record);
		$this->getTemplate()->add('recordStatus', $this->recordService->getRecordStatus($this->record));

		$this->getTemplate()->add('costume', $this->record->kostym);
		$this->getTemplate()->add('supplement', $this->record->doplnek);

		$this->getTemplate()->add('client', $this->record->klient);
		$this->getTemplate()->add('employee', $this->record->zamestnanec);
	}
}
