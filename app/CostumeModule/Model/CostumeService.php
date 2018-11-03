<?php

declare(strict_types=1);

namespace App\CostumeModule\Model;

final class CostumeService extends \IIS\Model\BaseService
{
	public function getTableName(): string
	{
		return 'kostym';
	}


	public function isCategoryUsed(\Nette\Database\Table\ActiveRow $category): bool
	{
		$costume = $this->fetch(function (\Nette\Database\Table\Selection $selection) use ($category): void {
			$selection->where('kategorie_id', $category->id);
		});

		return $costume !== null;
	}


	/**
	 * @throws \App\CostumeModule\Model\Exception
	 */
	public function addCostume(\Nette\Utils\ArrayHash $data): void
	{
		$insertData = [
			'vyrobce' => $data->manufacturer,
			'material' => $data->material,
			'popis' => $data->description,
			'cena' => $data->price,
			'datum_vyroby' => $data->createdDate,
			'opotrebeni' => $data->wear,
			'velikost' => $data->size,
			'barva' => $data->color,
			'dostupnost' => $data->availability,
			'obrazek' => $data->imageFile,
			'aktivni' => true,
			'kategorie_id' => $data->category,
			'zamestnanec_id' => $data->employee,
		];

		$costume = $this->getTable()->insert($insertData);

		if (!$costume instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\CostumeModule\Model\Exception('Kostým se nepodařilo vytvořit.');
		}
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editCostume(\Nette\Utils\ArrayHash $data): void
	{
		$updateData = [
			'vyrobce' => $data->manufacturer,
			'material' => $data->material,
			'popis' => $data->description,
			'cena' => $data->price,
			'datum_vyroby' => $data->createdDate,
			'opotrebeni' => $data->wear,
			'velikost' => $data->size,
			'barva' => $data->color,
			'dostupnost' => $data->availability,
			'kategorie_id' => $data->category,
			'zamestnanec_id' => $data->employee,
		];
		if ($data->imageFile) {
			$updateData['obrazek'] = $data->imageFile;
		}

		$this->getTable()->wherePrimary($data->id)->update($updateData);
	}


	public function deleteImage(\Nette\Database\Table\ActiveRow $costume): void
	{
		$costume->update([
			'obrazek' => null,
		]);
	}


	public function isCostumeReservable(\Nette\Database\Table\ActiveRow $costume): bool
	{
		return
			(bool) $costume->aktivni === true
			&& $costume->dostupnost === \App\CoreModule\Model\Availability::AVAILABILITY_AVAILABLE;
	}
}
