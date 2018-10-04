<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\Footer;

final class FooterControl extends \IIS\Application\UI\BaseControl
{
	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(\Nette\Security\User $user)
	{
		parent::__construct();
		$this->user = $user;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->getTemplate()->add('userType', $this->getUserType());
	}


	private function getUserType(): string
	{
		if ($this->user->isLoggedIn() && $identity = $this->user->getIdentity()) {
			/** @var $identity \Nette\Security\Identity */
			switch ($identity->getData()['typ']) {
				case \App\UserModule\Model\UserService::USER_TYPE_ADMIN:
					return 'Administrátor';

				case \App\UserModule\Model\UserService::USER_TYPE_EMPLOYEE:
					return 'Zaměstnanec';

				case \App\UserModule\Model\UserService::USER_TYPE_CLIENT:
					return 'Klient';
			}
		}

		return '';
	}
}
