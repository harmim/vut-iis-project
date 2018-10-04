<?php

declare(strict_types=1);

namespace App\UserModule\Presenters;

final class SignPresenter extends \App\CoreModule\Presenters\BasePresenter
{
	/**
	 * @var \App\UserModule\Controls\Login\ILoginControlFactory
	 */
	private $loginControlFactory;


	public function __construct(\App\UserModule\Controls\Login\ILoginControlFactory $loginControlFactory)
	{
		parent::__construct();
		$this->loginControlFactory = $loginControlFactory;
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionLogin(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':Core:Homepage:default');
		}
	}


	protected function createComponentLogin(): \App\UserModule\Controls\Login\LoginControl
	{
		return $this->loginControlFactory->create();
	}
}
