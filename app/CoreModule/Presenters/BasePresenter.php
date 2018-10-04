<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

abstract class BasePresenter extends \IIS\Application\UI\BasePresenter
{
	/**
	 * @var \App\CoreModule\Controls\Head\IHeadControlFactory
	 */
	private $headControlFactory;

	/**
	 * @var \App\CoreModule\Controls\Header\IHeaderControlFactory
	 */
	private $headerControlFactory;

	/**
	 * @var \App\CoreModule\Controls\Footer\IFooterControlFactory
	 */
	private $footerControlFactory;

	/**
	 * @var \App\CoreModule\Controls\FlashMessage\IFlashMessageControlFactory
	 */
	private $flashMessageControlFactory;


	public function injectHeadControlFactory(
		\App\CoreModule\Controls\Head\IHeadControlFactory $headControlFactory
	): void {
		$this->headControlFactory = $headControlFactory;
	}


	public function injectHeaderControlFactory(
		\App\CoreModule\Controls\Header\IHeaderControlFactory $headerControlFactory
	): void {
		$this->headerControlFactory = $headerControlFactory;
	}


	public function injectFooterControlFactory(
		\App\CoreModule\Controls\Footer\IFooterControlFactory $footerControlFactory
	): void {
		$this->footerControlFactory = $footerControlFactory;
	}


	public function injectFlashMessageControlFactory(
		\App\CoreModule\Controls\FlashMessage\IFlashMessageControlFactory $flashMessageControlFactory
	): void {
		$this->flashMessageControlFactory = $flashMessageControlFactory;
	}


	protected function createComponentHead(): \App\CoreModule\Controls\Head\HeadControl
	{
		return $this->headControlFactory->create();
	}


	protected function createComponentHeader(): \App\CoreModule\Controls\Header\HeaderControl
	{
		return $this->headerControlFactory->create();
	}


	protected function createComponentFooter(): \App\CoreModule\Controls\Footer\FooterControl
	{
		return $this->footerControlFactory->create();
	}


	protected function createComponentFlashMessage(): \App\CoreModule\Controls\FlashMessage\FlashMessageControl
	{
		return $this->flashMessageControlFactory->create();
	}


	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function handleLogout(): void
	{
		$this->getUser()->logout(true);
		$this->flashMessage('Byli jste úspěšně odhlášeni.', 'success');
		$this->redirect(\Nette\Http\IResponse::S303_SEE_OTHER, 'this');
	}
}
