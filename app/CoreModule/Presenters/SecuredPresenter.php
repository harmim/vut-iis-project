<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

abstract class SecuredPresenter extends \App\CoreModule\Presenters\BasePresenter
{
	/**
	 * @throws \Nette\Application\AbortException
	 */
	protected function startup(): void
	{
		parent::startup();
		$this->checkIsLoggedIn();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	protected function checkIsLoggedIn(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->getLogoutReason() === \Nette\Http\UserStorage::INACTIVITY) {
				$this->flashMessage('Byli jste odhlášeni kvůli delší neaktivitě. Prosím přihlašte se znovu.', 'info');
			}

			$this->redirect(':User:Sign:login', ['login-backLink' => $this->storeRequest()]);
		}
	}
}
