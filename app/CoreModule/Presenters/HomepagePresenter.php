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
		if ($this->getUser()->isAllowed('user.user', \App\UserModule\Model\AuthorizatorFactory::ACTION_LIST)) {
			$this->redirect(':User:User:list');

		} else {
			$this->redirect(':Record:Record:list');
		}
	}
}
