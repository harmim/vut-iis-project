<?php

declare(strict_types=1);

namespace App\BorrowModule\Model;

final class BorrowService extends \IIS\Model\BaseService
{
	public function getTableName(): string
	{
		return 'zaznam';
	}


	/**
	 * @throws \App\BorrowModule\Model\Exception
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
			throw new \App\BorrowModule\Model\Exception('Kostým se nepodařilo rezervovat.');
		}
	}
}
