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


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function checkPermission(?string $privilege = null, ?string $resource = null): bool
	{
		if ($privilege === null) {
			$privilege = $this->getAction();
		}

		if ($resource === null) {
			$resource = $this->getModuleName() . '.' . $this->getPresenterName();
		}

		if (!$this->getUser()->isAllowed($resource, $privilege)) {
			$this->flashMessage('Přístup zamítnut.', 'error');
			$this->redirect(':Core:Homepage:default');

			return false;
		}

		return true;
	}
}
