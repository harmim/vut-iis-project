<?php

declare(strict_types=1);

namespace App\CostumeModule\Model;

final class CostumeService extends \IIS\Model\BaseService
{
	public function getTableName(): string
	{
		return 'kostym';
	}


	public function isCategoryUsed(int $categoryId): bool
	{
		$costume = $this->fetch(function (\Nette\Database\Table\Selection $selection) use ($categoryId): void {
			$selection->where('kategorie_id', $categoryId);
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
			'aktivni' => true,
			'kategorie_id' => $data->category,
			'zamestnanec_id' => $data->employee,
		];
		if ($data->imageFile) {
			$updateData['obrazek'] = $data->imageFile;
		}

		$this->getTable()->wherePrimary($data->id)->update($updateData);
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function deleteImage(int $costumeId): void
	{
		$this->getTable()->wherePrimary($costumeId)->update([
			'obrazek' => null,
		]);
	}
}
