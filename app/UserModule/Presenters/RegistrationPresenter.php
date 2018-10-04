<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class RegistrationPresenter extends \App\CoreModule\Presenters\BasePresenter
{
	/**
	 * @var \App\UserModule\Controls\Registration\IRegistrationControlFactory
	 */
	private $registrationControlFactory;


	public function __construct(
		\App\UserModule\Controls\Registration\IRegistrationControlFactory $registrationControlFactory
	) {
		parent::__construct();
		$this->registrationControlFactory = $registrationControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDefault(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':Core:Homepage:default');
		}
	}


	protected function createComponentRegistration(): \App\UserModule\Controls\Registration\RegistrationControl
	{
		return $this->registrationControlFactory->create();
	}
}
