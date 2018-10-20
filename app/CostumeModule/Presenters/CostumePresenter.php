<?php

declare(strict_types=1);

namespace App\CostumeModule\Presenters;

final class CostumePresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \App\CostumeModule\Controls\CostumeListGrid\ICostumeListGridControlFactory
	 */
	private $costumeListGridControlFactory;

	/**
	 * @var \App\CostumeModule\Controls\AddCostume\IAddCostumeControlFactory
	 */
	private $addCostumeControlFactory;


	public function __construct(
		\App\CostumeModule\Controls\CostumeListGrid\ICostumeListGridControlFactory $costumeListGridControlFactory,
		\App\CostumeModule\Controls\AddCostume\IAddCostumeControlFactory $addCostumeControlFactory
	) {
		parent::__construct();
		$this->costumeListGridControlFactory = $costumeListGridControlFactory;
		$this->addCostumeControlFactory = $addCostumeControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionList(): void
	{
		$this->checkPermission();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionAdd(): void
	{
		$this->checkPermission();
	}


	protected function createComponentCostumeListGrid(
	): \App\CostumeModule\Controls\CostumeListGrid\CostumeListGridControl {
		return $this->costumeListGridControlFactory->create();
	}


	protected function createComponentAddCostume(): \App\CostumeModule\Controls\AddCostume\AddCostumeControl
	{
		return $this->addCostumeControlFactory->create();
	}
}
