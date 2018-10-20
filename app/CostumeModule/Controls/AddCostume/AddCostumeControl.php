<?php

declare(strict_types=1);

namespace App\CostumeModule\Controls\AddCostume;

final class AddCostumeControl extends \IIS\Application\UI\BaseControl
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
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(
		\App\CostumeModule\Model\CostumeFormFactory $costumeFormFactory,
		\App\CostumeModule\Model\CostumeService $costumeService,
		\Nette\Security\User $user
	) {
		parent::__construct();
		$this->costumeFormFactory = $costumeFormFactory;
		$this->costumeService = $costumeService;
		$this->user = $user;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 */
	protected function createComponentAddForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->costumeFormFactory->createCostumeForm();

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
			$this->costumeService->addCostume($values);
		} catch (\App\CostumeModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			if ($values->imageFile) {
				$this->imageStorage->deleteImage($values->imageFile);
			}
			return;
		}

		$presenter->flashMessage('Kostým byl úspěšně vytvořen.', 'success');
		$presenter->redirect(':Costume:Costume:list');
	}
}
