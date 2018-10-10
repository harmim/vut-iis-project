<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\FlashMessage;

final class FlashMessageControl extends \IIS\Application\UI\BaseControl
{
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$parent = $this->getParent();
		if ($parent instanceof \Nette\Application\UI\Control) {
			$flashes = $parent->getTemplate()->flashes;
			$presenterFlashes = [];
			if (!$parent instanceof \Nette\Application\UI\Presenter) {
				$presenter = $parent->getPresenter();
				if ($presenter) {
					$presenterFlashes = $presenter->getTemplate()->flashes;
				}
			}

			$this->getTemplate()->setParameters([
				'flashes' => \array_merge($flashes, $presenterFlashes),
			]);
		}
	}
}
