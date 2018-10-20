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
	public function addCostume(\Nette\Utils\ArrayHash $data, int $employeeId): void
	{
		$insertData = [
			'vyrobce' => $data->manufacturer,
			'material' => $data->material,
			'popis' => $data->description,
			'cena' => $data->price,
			'datum_vyroby' => $data->createdDate,
			'velikost' => $data->size,
			'barva' => $data->color,
			'dostupnost' => $data->availability,
			'aktivni' => true,
			'kategorie_id' => $data->category,
			'zamestnanec_id' => $employeeId,
		];
		if ($data->wear) {
			$insertData['opotrebeni'] = $data->wear;
		}
		if ($data->imageFile) {
			$insertData['obrazek'] = $data->imageFile;
		}

		$costume = $this->getTable()->insert($insertData);

		if (!$costume instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\CostumeModule\Model\Exception('Kostým se nepodařilo vytvořit.');
		}
	}
}
