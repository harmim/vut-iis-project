<?php

declare(strict_types=1);

namespace App\UserModule\Controls\EditClient;

final class EditClientControl extends \IIS\Application\UI\BaseControl
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
		$form = $this->userFormFactory->createClientForm();

		/** @var \Nette\Forms\Controls\TextInput $password */
		$password = $form['password'];
		$password->setRequired(false);

		$client = $this->user->klient;
		$form->addHidden('id', $this->user->id);
		$form->addHidden('clientId', $client->id);

		$company = $this->userService->getCompanyTable()->get($client->id);
		$useCompany = !empty($company->ico);
		$form->setDefaults([
			'email' => $this->user->email,
			'firstName' => $client->jmeno,
			'lastName' => $client->prijmeni,
			'bornDate' => $client->datum_narozeni,
			'phone' => $client->telefonni_cislo,
			'address' => $client->adresa,
			'useCompany' => $useCompany,
		]);
		if ($company instanceof \Nette\Database\Table\ActiveRow) {
			$form->setDefaults([
				'ico' => $useCompany ? $company->ico : null,
				'dic' => $useCompany ? $company->dic : null,
				'companyAddress' => $useCompany ? $company->fakturacni_adresa : null,
			]);
		}

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
		if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
			$presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_EDIT);
		}

		try {
			$this->userService->editClient($values);
		} catch (\App\UserModule\Model\Exception $e) {
			$form->addError($e->getMessage());
			return;
		}

		if ($presenter) {
			$presenter->flashMessage('Klient byl úspěšně uložen.', 'success');
			$presenter->redirect('this');
		}
	}
}
