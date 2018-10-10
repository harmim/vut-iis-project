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
		if ($this->user->isLoggedIn()) {
			$identity = $this->user->getIdentity();
			if ($identity instanceof \Nette\Security\Identity) {
				return \App\UserModule\Model\UserService::ROLE_TRANSLATION_MAP[$identity->typ];
			}
		}

		return '';
	}
}
