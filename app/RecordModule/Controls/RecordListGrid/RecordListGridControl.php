<?php

declare(strict_types=1);

namespace App\RecordModule\Controls\RecordListGrid;

final class RecordListGridControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
	 */
	private $dataGridControlFactory;

	/**
	 * @var \App\RecordModule\Model\RecordService
	 */
	private $recordService;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;


	public function __construct(
		\App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
		\App\RecordModule\Model\RecordService $recordService,
		\Nette\Security\User $user,
		\App\UserModule\Model\UserService $userService
	) {
		parent::__construct();
		$this->dataGridControlFactory = $dataGridControlFactory;
		$this->recordService = $recordService;
		$this->user = $user;
		$this->userService = $userService;
	}


	/**
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
	{
		$grid = $this->dataGridControlFactory->create($this->recordService, [$this, 'defaultFilter']);

		$grid->addColumnText('actionName', 'Název akce', 'nazev_akce')
			->setFilterText();

		$grid->addColumnText('status', 'Stav')
			->setRenderer([$this, 'renderStatus'])
			->setFilterSelect(\App\RecordModule\Model\RecordService::RECORD_STATUSES)
				->setCondition([$this, 'filterStatus']);

		$grid->addColumnDateTime('borrowDate', 'Datum zapujčení', 'datum_zapujceni')
			->setFilterDateRange();

		$grid->addColumnText('costumeDescription', 'Kostým', 'kostym.popis')
			->setRenderer([$this, 'renderCostumeDescription'])
			->setFilterText();

		$grid->addColumnText('supplementName', 'Doplněk', 'doplnek.nazev')
			->setRenderer([$this, 'renderSupplementName'])
			->setFilterText();

		if (!$this->user->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT)) {
			$grid->addColumnText('employeeEmail', 'Zaměstnanec', 'zamestnanec_id')
				->setRenderer([$this, 'renderEmployeeEmail'])
				->setFilterText()
					->setCondition([$this, 'filterEmployeeEmail']);

			$grid->addColumnText('clientEmail', 'Klient', 'klient_id')
				->setRenderer([$this, 'renderClientEmail'])
				->setFilterText()
					->setCondition([$this, 'filterClientEmail']);
		}

		$actionDetail = $grid->addAction('detail', '', ':Record:Record:default');
		$actionDetail->setTitle('Detail')
			->setClass('btn btn-xs btn-primary')
			->setIcon('eye');

		return $grid;
	}


	public function defaultFilter(\Nette\Database\Table\Selection $selection): void
	{
		if ($this->user->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT)) {
			$identity = $this->user->getIdentity();
			if ($identity instanceof \Nette\Security\Identity) {
				$selection->where('klient_id', $identity->klient_id);
			}
		}
	}


	public function renderStatus(\Nette\Database\Table\ActiveRow $record): string
	{
		return \App\RecordModule\Model\RecordService::RECORD_STATUSES[$this->recordService->getRecordStatus($record)];
	}


	public function renderCostumeDescription(\Nette\Database\Table\ActiveRow $record): string
	{
		return $record->kostym ? \Nette\Utils\Strings::truncate((string) $record->kostym->popis, 20) : '';
	}


	public function renderSupplementName(\Nette\Database\Table\ActiveRow $record): string
	{
		return $record->doplnek ? \Nette\Utils\Strings::truncate((string) $record->doplnek->nazev, 20) : '';
	}


	/**
	 * @throws \Nette\MemberAccessException
	 */
	public function renderEmployeeEmail(\Nette\Database\Table\ActiveRow $record): string
	{
		$employee = $record->zamestnanec;
		if (!$employee instanceof \Nette\Database\Table\ActiveRow) {
			return '';
		}

		$user = $employee->related('uzivatel.zamestnanec_id')->fetch();
		if (!$user) {
			return '';
		}

		return (string) $user->email;
	}


	/**
	 * @throws \Nette\MemberAccessException
	 */
	public function renderClientEmail(\Nette\Database\Table\ActiveRow $record): string
	{
		$client = $record->klient;
		if (!$client instanceof \Nette\Database\Table\ActiveRow) {
			return '';
		}

		$user = $client->related('uzivatel.klient_id')->fetch();
		if (!$user) {
			return '';
		}

		return (string) $user->email;
	}


	public function filterStatus(\Nette\Database\Table\Selection $selection, string $status): void
	{
		switch ($status) {
			case \App\RecordModule\Model\RecordService::RECORD_STATUS_CLOSED:
				$selection->where('datum_vraceni NOT ?', null);
				return;

			case \App\RecordModule\Model\RecordService::RECORD_STATUS_CONFIRMED:
				$selection->where('zamestnanec_id NOT ?', null);
				$selection->where('datum_vraceni', null);
				return;

			case \App\RecordModule\Model\RecordService::RECORD_STATUS_RESERVATION:
				$selection->where('zamestnanec_id', null);
				$selection->where('datum_vraceni', null);
				return;
		}
	}


	public function filterEmployeeEmail(\Nette\Database\Table\Selection $selection, string $employeeEmail): void
	{
		$employeeIds = $this->userService->getTable()
			->where('email LIKE ?', "%$employeeEmail%")
			->fetchPairs('id', 'zamestnanec_id');
		$selection->where('zamestnanec_id', $employeeIds);
	}


	public function filterClientEmail(\Nette\Database\Table\Selection $selection, string $clientEmail): void
	{
		$clientIds = $this->userService->getTable()
			->where('email LIKE ?', "%$clientEmail%")
			->fetchPairs('id', 'klient_id');
		$selection->where('klient_id', $clientIds);
	}
}
