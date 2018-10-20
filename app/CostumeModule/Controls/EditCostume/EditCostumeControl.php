<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\EditCostume;

final class EditCostumeControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\CostumeModule\Model\CostumeFormFactory
	 */
	private $costumeFormFactory;

	/**
	 * @var \App\CostumeModule\Model\CostumeService
	 */
	private $costumeService;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $costume;


	public function __construct(
		\App\CostumeModule\Model\CostumeFormFactory $costumeFormFactory,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\Nette\Database\Table\ActiveRow $costume
	) {
		parent::__construct();
		$this->costumeFormFactory = $costumeFormFactory;
		$this->costumeService = $costumeService;
		$this->costume = $costume;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('costume', $this->costume);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function handleDeleteImage(int $costumeId): void
	{
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_DELETE);
		}

		$costume = $this->costumeService->fetchById($costumeId);
		if (!$costume) {
			$presenter->error();
			return;
		}

		$this->imageStorage->deleteImage($costume->obrazek);
		$this->costumeService->deleteImage($costumeId);

		$presenter->flashMessage('Obrázek byl úspěšně smazán', 'success');
		$presenter->redirect('this');
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	protected function createComponentEditForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->costumeFormFactory->createCostumeForm();

		$form->setDefaults([
			'manufacturer' => $this->costume->vyrobce,
			'material' => $this->costume->material,
			'description' => $this->costume->popis,
			'employee' => $this->costume->zamestnanec_id,
			'price' => $this->costume->cena,
			'createdDate' => $this->costume->datum_vyroby,
			'wear' => $this->costume->opotrebeni,
			'size' => $this->costume->velikost,
			'color' => $this->costume->barva,
			'availability' => $this->costume->dostupnost,
			'category' => $this->costume->kategorie_id,
		]);

		$form->addHidden('id', $this->costume->id);

		$form->addSubmit('save', 'Uložit')
			->setAttribute('class', 'btn btn-primary btn-block');

		$form->onSuccess[] = [$this, 'onSuccessEditForm'];

		return $form;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function onSuccessEditForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
		}

		/** @var \Nette\Http\FileUpload $image */
		$image = $values->image;
		$values->imageFile = null;

		if ($image->isOk() && $image->isImage()) {
			try {
				$values->imageFile = $this->imageStorage->saveUpload($image);
			} catch (\Exception $e) {
				\Tracy\Debugger::log($e);
				$presenter->flashMessage('Obrázek se nepodařilo nahrát.', 'error');
				return;
			}
		}

		$previousImage = null;
		if ($values->imageFile) {
			$costume = $this->costumeService->fetchById((int) $values->id);
			if ($costume) {
				$previousImage = $costume->obrazek;
			}
		}

		$this->costumeService->editCostume($values);

		if ($previousImage) {
			$this->imageStorage->deleteImage($previousImage);
		}

		$presenter->flashMessage('Kostým byl úspěšně uložen.', 'success');
		$presenter->redirect('this');
	}
}
