<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

final class HomepagePresenter extends \App\CoreModule\Presenters\SecuredPresenter
{
	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDefault(): void
	{
		if ($this->getUser()->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN)) {
			$this->redirect(':User:User:list');
			return;
		}

		if ($this->getUser()->isInRole(\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT)) {
			$this->redirect(':Costume:Costume:list');
			return;
		}
	}
}
