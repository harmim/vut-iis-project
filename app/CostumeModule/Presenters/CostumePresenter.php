<?php

declare(strict_types=1);

namespace App\CostumeModule\Presenters;

final class CostumePresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $editedCostume;

	/**
	 * @var \App\CostumeModule\Controls\CostumeListGrid\ICostumeListGridControlFactory
	 */
	private $costumeListGridControlFactory;

	/**
	 * @var \App\CostumeModule\Controls\AddCostume\IAddCostumeControlFactory
	 */
	private $addCostumeControlFactory;

	/**
	 * @var \App\CostumeModule\Controls\EditCostume\IEditCostumeControlFactory
	 */
	private $editCostumeControlFactory;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;

	/**
	 * @var \App\CostumeModule\Controls\CostumeDetail\ICostumeDetailControlFactory
	 */
	private $costumeDetailControlFactory;

	/**
	 * @var \App\SupplementModule\Controls\SupplementListGrid\ISupplementListGridControlFactory
	 */
	private $supplementListGridControlFactory;


	public function __construct(
		\App\CostumeModule\Controls\CostumeListGrid\ICostumeListGridControlFactory $costumeListGridControlFactory,
		\App\CostumeModule\Controls\AddCostume\IAddCostumeControlFactory $addCostumeControlFactory,
		\App\CostumeModule\Controls\EditCostume\IEditCostumeControlFactory $editCostumeControlFactory,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\App\CostumeModule\Controls\CostumeDetail\ICostumeDetailControlFactory $costumeDetailControlFactory,
		\App\SupplementModule\Controls\SupplementListGrid\ISupplementListGridControlFactory $supplementListGridControlFactory
	) {
		parent::__construct();
		$this->costumeListGridControlFactory = $costumeListGridControlFactory;
		$this->addCostumeControlFactory = $addCostumeControlFactory;
		$this->editCostumeControlFactory = $editCostumeControlFactory;
		$this->costumeService = $costumeService;
		$this->costumeDetailControlFactory = $costumeDetailControlFactory;
		$this->supplementListGridControlFactory = $supplementListGridControlFactory;
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


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionEdit(int $id): void
	{
		$this->checkPermission();

		$this->editedCostume = $this->costumeService->fetchById($id);
		if (!$this->editedCostume) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDefault(int $id): void
	{
		$this->checkPermission();

		$this->editedCostume = $this->costumeService->fetchById($id);
		if (
			!$this->editedCostume
			|| (
				!$this->editedCostume->aktivni
				&& !$this->getUser()->isAllowed(
					'costume.costume',
					\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT
				)
			)
		) {
			$this->error();
			return;
		}
	}


	public function renderDefault(): void
	{
		$this->getTemplate()->add('editedCostume', $this->editedCostume);
	}


	protected function createComponentCostumeListGrid(
	): \App\CostumeModule\Controls\CostumeListGrid\CostumeListGridControl {
		return $this->costumeListGridControlFactory->create();
	}


	protected function createComponentAddCostume(): \App\CostumeModule\Controls\AddCostume\AddCostumeControl
	{
		return $this->addCostumeControlFactory->create();
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditCostume(): ?\App\CostumeModule\Controls\EditCostume\EditCostumeControl
	{
		if (!$this->editedCostume) {
			$this->error();
			return null;
		}

		return $this->editCostumeControlFactory->create($this->editedCostume);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentCostumeDetail(): ?\App\CostumeModule\Controls\CostumeDetail\CostumeDetailControl
	{
		if (!$this->editedCostume) {
			$this->error();
			return null;
		}

		return $this->costumeDetailControlFactory->create($this->editedCostume);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentSupplementListGrid(
	): ?\App\SupplementModule\Controls\SupplementListGrid\SupplementListGridControl {
		if (!$this->editedCostume) {
			$this->error();
			return null;
		}

		return $this->supplementListGridControlFactory->create($this->editedCostume);
	}
}
