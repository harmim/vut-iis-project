<?php

declare(strict_types=1);

namespace IIS\Model;

abstract class BaseService
{
	use \Nette\SmartObject;

	/**
	 * @var \Nette\Database\Context
	 */
	protected $database;


	public function __construct(\Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	abstract public function getTableName(): string;


	public function getTable(): \Nette\Database\Table\Selection
	{
		return $this->database->table($this->getTableName());
	}


	public function selectionCallback(?callable $callback): \Nette\Database\Table\Selection
	{
		$selection = $this->getTable();
		if ($callback) {
			$callback($selection);
		}

		return $selection;
	}


	public function fetch(callable $callback = null): ?\Nette\Database\Table\ActiveRow
	{
		return $this->selectionCallback($callback)->fetch() ?: null;
	}


	public function fetchById(int $id): ?\Nette\Database\Table\ActiveRow
	{
		return $this->getTable()->get($id) ?: null;
	}


	/**
	 * @return array|\Nette\Database\Table\ActiveRow[]
	 */
	public function fetchAll(callable $callback = null): array
	{
		return $this->selectionCallback($callback)->fetchAll();
	}


	public function fetchPairs(string $key = null, string $value = null, callable $callback = null): array
	{
		return $this->selectionCallback($callback)->fetchPairs($key, $value);
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function changeActive(int $id, bool $active): void
	{
		$this->getTable()->wherePrimary($id)->update([
			'aktivni' => $active,
		]);
	}
}
