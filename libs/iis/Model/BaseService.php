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
}
