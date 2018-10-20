<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\CostumeDetail;

final class CostumeDetailControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $costume;


	public function __construct(\Nette\Database\Table\ActiveRow $costume)
	{
		parent::__construct();
		$this->costume = $costume;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('costume', $this->costume);
	}
}
