<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

final class ErrorPresenter implements \Nette\Application\IPresenter
{
	use \Nette\SmartObject;

	/**
	 * @var \Tracy\ILogger
	 */
	private $logger;


	public function __construct(\Tracy\ILogger $logger)
	{
		$this->logger = $logger;
	}


	public function run(\Nette\Application\Request $request)
	{
		$exception = $request->getParameter('exception');

		if ($exception instanceof \Nette\Application\BadRequestException) {
			$request->setPresenterName('Core:Error4xx');
			return new \Nette\Application\Responses\ForwardResponse($request);
		}

		$this->logger->log($exception, \Tracy\ILogger::EXCEPTION);

		return new \Nette\Application\Responses\CallbackResponse(function (): void {
			require __DIR__ . '/../templates/Error500/default.phtml';
		});
	}
}
