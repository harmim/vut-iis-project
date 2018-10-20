<?php

declare(strict_types=1);

namespace App\CostumeModule\Model;

final class CategoryService extends \IIS\Model\BaseService
{
	public function getTableName(): string
	{
		return 'kategorie';
	}


	/**
	 * @throws \App\CostumeModule\Model\Exception
	 */
	public function addCategory(\Nette\Utils\ArrayHash $data): void
	{
		$category = $this->getTable()->insert([
			'nazev' => $data->name,
			'popis' => $data->description,
		]);

		if (!$category instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\CostumeModule\Model\Exception('Kategorii se nepodařilo vytvořit.');
		}
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editCategory(\Nette\Utils\ArrayHash $data): void
	{
		$this->getTable()->wherePrimary($data->id)->update([
			'nazev' => $data->name,
			'popis' => $data->description,
		]);
	}
}
