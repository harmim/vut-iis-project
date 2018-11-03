<?php

declare(strict_types=1);

namespace App\SupplementModule\Model;

final class SupplementService extends \IIS\Model\BaseService
{
	public function getTableName(): string
	{
		return 'doplnek';
	}


	public function isSupplementReservable(\Nette\Database\Table\ActiveRow $supplement): bool
	{
		return
			(bool) $supplement->aktivni === true
			&& $supplement->dostupnost === \App\CoreModule\Model\Availability::AVAILABILITY_AVAILABLE;
	}


	public function deleteImage(\Nette\Database\Table\ActiveRow $supplement): void
	{
		$supplement->update([
			'obrazek' => null,
		]);
	}


	/**
	 * @throws \App\SupplementModule\Model\Exception
	 */
	public function addSupplement(\Nette\Database\Table\ActiveRow $costume, \Nette\Utils\ArrayHash $data): void
	{
		$insertData = [
			'nazev' => $data->name,
			'popis' => $data->description,
			'datum_vyroby' => $data->createdDate,
			'cena' => $data->price,
			'dostupnost' => $data->availability,
			'obrazek' => $data->imageFile,
			'aktivni' => true,
			'zamestnanec_id' => $data->employee,
			'kostym_id' => $costume->id,
		];

		$supplement = $this->getTable()->insert($insertData);

		if (!$supplement instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\SupplementModule\Model\Exception('Doplněk se nepodařilo vytvořit.');
		}
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editSupplement(\Nette\Utils\ArrayHash $data): void
	{
		$updateData = [
			'nazev' => $data->name,
			'popis' => $data->description,
			'datum_vyroby' => $data->createdDate,
			'cena' => $data->price,
			'dostupnost' => $data->availability,
			'zamestnanec_id' => $data->employee,
		];
		if ($data->imageFile) {
			$updateData['obrazek'] = $data->imageFile;
		}

		$this->getTable()->wherePrimary($data->id)->update($updateData);
	}
}
