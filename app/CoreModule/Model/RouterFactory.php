<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

final class RouterFactory
{
	use \Nette\StaticClass;

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function createRouter(): \Nette\Application\IRouter
	{
		$router = new \Nette\Application\Routers\RouteList();
		$router[] = new \Nette\Application\Routers\Route('[<module>[/<presenter>[/<action>[/<id \d+>]]]]', [
			'module' => 'Core',
			'presenter' => 'Homepage',
			'action' => 'default',
		]);

		return $router;
	}
}
