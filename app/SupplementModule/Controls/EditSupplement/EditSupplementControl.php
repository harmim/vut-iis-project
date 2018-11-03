<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\EditSupplement;

final class EditSupplementControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\SupplementModule\Model\SupplementFormFactory
	 */
	private $supplementFormFactory;

	/**
	 * @var \App\SupplementModule\Model\SupplementService
	 */
	private $supplementService;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $supplement;


	public function __construct(
		\App\SupplementModule\Model\SupplementFormFactory $supplementFormFactory,
		\App\SupplementModule\Model\SupplementService $supplementService,
		\Nette\Database\Table\ActiveRow $supplement
	) {
		parent::__construct();
		$this->supplementFormFactory = $supplementFormFactory;
		$this->supplementService = $supplementService;
		$this->supplement = $supplement;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('supplement', $this->supplement);
	}


	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function handleDeleteImage(int $supplementId): void
	{
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_DELETE);
		}

		$supplement = $this->supplementService->fetchById($supplementId);
		if (!$supplement) {
			$presenter->error();
			return;
		}

		$this->imageStorage->deleteImage($supplement->obrazek);
		$this->supplementService->deleteImage($supplement);

		$presenter->flashMessage('Obrázek byl úspěšně smazán.', 'success');
		$presenter->redirect('this');
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	protected function createComponentEditForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->supplementFormFactory->createSupplementForm();

		$form->setDefaults([
			'name' => $this->supplement->nazev,
			'description' => $this->supplement->popis,
			'createdDate' => $this->supplement->datum_vyroby,
			'price' => $this->supplement->cena,
			'availability' => $this->supplement->dostupnost,
			'employee' => $this->supplement->zamestnanec_id,
		]);

		$form->addHidden('id', $this->supplement->id);

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
			$previousImage = $this->supplement->obrazek;
		}

		$this->supplementService->editSupplement($values);

		if ($previousImage) {
			$this->imageStorage->deleteImage($previousImage);
		}

		$presenter->flashMessage('Doplněk byl úspěšně uložen.', 'success');
		$presenter->redirect('this');
	}
}
