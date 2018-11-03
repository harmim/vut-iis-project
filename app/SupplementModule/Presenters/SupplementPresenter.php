<?php

declare(strict_types=1);

namespace App\SupplementModule\Presenters;

final class SupplementPresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $editedSupplement;

	/**
	 * @var \Nette\Database\Table\ActiveRow|null
	 */
	private $relatedCostume;

	/**
	 * @var \App\SupplementModule\Model\SupplementService
	 */
	private $supplementService;

	/**
	 * @var \App\SupplementModule\Controls\SupplementDetail\ISupplementDetailControlFactory
	 */
	private $supplementDetailControlFactory;

	/**
	 * @var \App\SupplementModule\Controls\AddSupplement\IAddSupplementControlFactory
	 */
	private $addSupplementControlFactory;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;

	/**
	 * @var \App\SupplementModule\Controls\EditSupplement\IEditSupplementControlFactory
	 */
	private $editSupplementControlFactory;


	public function __construct(
		\App\SupplementModule\Model\SupplementService $supplementService,
		\App\SupplementModule\Controls\SupplementDetail\ISupplementDetailControlFactory $supplementDetailControlFactory,
		\App\SupplementModule\Controls\AddSupplement\IAddSupplementControlFactory $addSupplementControlFactory,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\App\SupplementModule\Controls\EditSupplement\IEditSupplementControlFactory $editSupplementControlFactory
	) {
		parent::__construct();
		$this->supplementService = $supplementService;
		$this->supplementDetailControlFactory = $supplementDetailControlFactory;
		$this->addSupplementControlFactory = $addSupplementControlFactory;
		$this->costumeService = $costumeService;
		$this->editSupplementControlFactory = $editSupplementControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDefault(int $id): void
	{
		$this->checkPermission();

		$this->editedSupplement = $this->supplementService->fetchById($id);
		if (
			!$this->editedSupplement
			|| (
				!$this->editedSupplement->aktivni
				&& !$this->getUser()->isAllowed(
					'supplement.supplement',
					\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT
				)
			)
		) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionAdd(int $costumeId): void
	{
		$this->checkPermission();

		$this->relatedCostume = $this->costumeService->fetchById($costumeId);
		if (!$this->relatedCostume) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionEdit(int $id): void
	{
		$this->checkPermission();

		$this->editedSupplement = $this->supplementService->fetchById($id);
		if (!$this->editedSupplement) {
			$this->error();
			return;
		}
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentSupplementDetail(
	): ?\App\SupplementModule\Controls\SupplementDetail\SupplementDetailControl {
		if (!$this->editedSupplement) {
			$this->error();
			return null;
		}

		return $this->supplementDetailControlFactory->create($this->editedSupplement);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentAddSupplement(
	): ?\App\SupplementModule\Controls\AddSupplement\AddSupplementControl {
		if (!$this->relatedCostume) {
			$this->error();
			return null;
		}

		return $this->addSupplementControlFactory->create($this->relatedCostume);
	}


	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function createComponentEditSupplement(
	): ?\App\SupplementModule\Controls\EditSupplement\EditSupplementControl {
		if (!$this->editedSupplement) {
			$this->error();
			return null;
		}

		return $this->editSupplementControlFactory->create($this->editedSupplement);
	}
}
