<?php

declare(strict_types=1);

namespace App\SupplementModule\Controls\AddSupplement;

final class AddSupplementControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $costume;

	/**
	 * @var \App\SupplementModule\Model\SupplementFormFactory
	 */
	private $supplementFormFactory;

	/**
	 * @var \App\SupplementModule\Model\SupplementService
	 */
	private $supplementService;


	public function __construct(
		\Nette\Database\Table\ActiveRow $costume,
		\App\SupplementModule\Model\SupplementFormFactory $supplementFormFactory,
		\App\SupplementModule\Model\SupplementService $supplementService
	) {
		parent::__construct();
		$this->costume = $costume;
		$this->supplementFormFactory = $supplementFormFactory;
		$this->supplementService = $supplementService;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('costume', $this->costume);
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	protected function createComponentAddForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->supplementFormFactory->createSupplementForm();

		$form->addSubmit('add', 'Vytvořit')
			->setAttribute('class', 'btn btn-primary btn-block');

		$form->onSuccess[] = [$this, 'onSuccessAddForm'];

		return $form;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function onSuccessAddForm(
		\Czubehead\BootstrapForms\BootstrapForm $form,
		\Nette\Utils\ArrayHash $values
	): void {
		$presenter = $this->getPresenter();
		if (!$presenter) {
			return;
		}

		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_ADD);
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

		try {
			$this->supplementService->addSupplement($this->costume, $values);
		} catch (\App\SupplementModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			if ($values->imageFile) {
				$this->imageStorage->deleteImage($values->imageFile);
			}
			return;
		}

		$presenter->flashMessage('Doplněk byl úspěšně vytvořen.', 'success');
		$presenter->redirect(':Costume:Costume:default', $this->costume->id);
	}
}
