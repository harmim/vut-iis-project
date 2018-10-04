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
			/** @var \Nette\Bridges\ApplicationLatte\Template $template */
			$template = $parent->getTemplate();
			$flashes = $template->getParameters()['flashes'];

			$presenterFlashes = [];
			if (!$parent instanceof \Nette\Application\UI\Presenter) {
				/** @var \Nette\Bridges\ApplicationLatte\Template $presenterTemplate */
				$presenterTemplate = $parent->getPresenter()->getTemplate();
				$presenterFlashes = $presenterTemplate->getParameters()['flashes'];
			}

			$this->getTemplate()->setParameters([
				'flashes' => \array_merge($flashes, $presenterFlashes),
			]);
		}
	}
}
