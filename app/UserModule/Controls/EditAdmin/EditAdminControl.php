<?php

declare(strict_types=1);

namespace App\UserModule\Controls\EditAdmin;

final class EditAdminControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \App\UserModule\Model\UserFormFactory
	 */
	private $userFormFactory;

	/**
	 * @var \App\UserModule\Model\UserService
	 */
	private $userService;

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	private $user;


	public function __construct(
		\App\UserModule\Model\UserFormFactory $userFormFactory,
		\App\UserModule\Model\UserService $userService,
		\Nette\Database\Table\ActiveRow $user
	) {
		parent::__construct();
		$this->userFormFactory = $userFormFactory;
		$this->userService = $userService;
		$this->user = $user;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function createComponentEditForm(): \Czubehead\BootstrapForms\BootstrapForm
	{
		$form = $this->userFormFactory->createAdminForm();

		/** @var \Nette\Forms\Controls\TextInput $password */
		$password = $form['password'];
		$password->setRequired(false);

		$form->addHidden('id', $this->user->id);

		$form->setDefaults([
			'email' => $this->user->email,
		]);

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
		try {
			$this->userService->editAdmin($values);
		} catch (\App\UserModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		$presenter = $this->getPresenter();
		if ($presenter) {
			$presenter->flashMessage('Administrátor byl úspěšně uložen.', 'success');
			$presenter->redirect('this');
		}
	}
}
