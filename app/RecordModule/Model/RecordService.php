<?php

declare(strict_types=1);

namespace App\RecordModule\Model;

final class RecordService extends \IIS\Model\BaseService
{
	public const RECORD_STATUS_RESERVATION = 'reservation',
		RECORD_STATUS_CONFIRMED = 'confirmed',
		RECORD_STATUS_CLOSED = 'closed';

	public const RECORD_STATUSES = [
		self::RECORD_STATUS_RESERVATION => 'Rezervováno',
		self::RECORD_STATUS_CONFIRMED => 'Zapůjčeno',
		self::RECORD_STATUS_CLOSED => 'Vráceno',
	];


	public function getTableName(): string
	{
		return 'zaznam';
	}


	/**
	 * @throws \App\RecordModule\Model\Exception
	 */
	public function makeCostumeReservation(
		\Nette\Database\Table\ActiveRow $costume,
		string $eventName,
		int $clientId,
		\DateTimeInterface $borrowDate
	): void {
		$insertData = [
			'nazev_akce' => $eventName,
			'datum_zapujceni' => $borrowDate,
			'cena' => $costume->cena,
			'kostym_id' => $costume->id,
			'klient_id' => $clientId,
		];

		$reservation = $this->getTable()->insert($insertData);

		if (!$reservation instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\RecordModule\Model\Exception('Kostým se nepodařilo rezervovat.');
		}
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function closeRecord(\Nette\Database\Table\ActiveRow $record, \DateTimeInterface $returnDate): void
	{
		$this->getTable()->wherePrimary($record->id)->update([
			'datum_vraceni' => $returnDate,
		]);
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function confirmReservation(\Nette\Database\Table\ActiveRow $record, int $employeeId): void
	{
		$this->getTable()->wherePrimary($record->id)->update([
			'zamestnanec_id' => $employeeId,
		]);
	}


	public function getRecordStatus(\Nette\Database\Table\ActiveRow $record): string
	{
		if (isset($record->datum_vraceni)) {
			return self::RECORD_STATUS_CLOSED;

		} elseif (isset($record->zamestnanec_id)) {
			return self::RECORD_STATUS_CONFIRMED;

		} else {
			return self::RECORD_STATUS_RESERVATION;
		}
	}
}
