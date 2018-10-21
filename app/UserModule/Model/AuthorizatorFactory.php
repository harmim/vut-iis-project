<?php

declare(strict_types=1);

namespace App\UserModule\Model;

final class AuthorizatorFactory
{
	use \Nette\StaticClass;

	public const ROLE_ADMIN = 'admin',
		ROLE_EMPLOYEE = 'zamestnanec',
		ROLE_CLIENT = 'klient';

	public const ACTION_ADD = 'add',
		ACTION_EDIT = 'edit',
		ACTION_LIST = 'list',
		ACTION_DELETE = 'delete';


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function create(
		array $roles,
		string $appDir
	): \Nette\Security\IAuthorizator {
		$permission = new \Nette\Security\Permission();
		self::setupRoles($permission);
		self::setupResources($permission, $appDir);
		self::setupRules($permission, $roles);

		return $permission;
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	private static function setupRoles(\Nette\Security\Permission $permission): void
	{
		$permission->addRole(self::ROLE_CLIENT);
		$permission->addRole(self::ROLE_EMPLOYEE, self::ROLE_CLIENT);
		$permission->addRole(self::ROLE_ADMIN, self::ROLE_EMPLOYEE);
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	private static function setupResources(\Nette\Security\Permission $permission, string $appDir): void
	{
		/** @var \SplFileInfo $dir */
		foreach (\Nette\Utils\Finder::findDirectories('*Module')->in($appDir) as $dir) {
			\preg_match('~^(.+)Module\z~', $dir->getFilename(), $matches);
			$moduleName = \strtolower($matches[1]);

			$presentersDir = $dir->getRealPath() . \DIRECTORY_SEPARATOR . 'Presenters';
			if (\is_dir($presentersDir)) {
				/** @var \SplFileInfo $presenter */
				foreach (\Nette\Utils\Finder::findFiles()->in($presentersDir) as $presenter) {
					\preg_match('~^(.+)Presenter.php\z~', $presenter->getFilename(), $matches);
					$presenterName = \strtolower($matches[1]);

					$permission->addResource("$moduleName.$presenterName");
				}
			}
		}
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	private static function setupRules(\Nette\Security\Permission $permission, array $roles): void
	{
		foreach ($roles as $role => $roleData) {
			if (!$permission->hasRole($role)) {
				$permission->addRole($role, $roleData['parent'] ?? null);
			}

			if (isset($roleData['resources']) && \is_array($roleData['resources'])) {
				if (!$roleData['resources']) {
					$permission->allow($role, \Nette\Security\IAuthorizator::ALL, \Nette\Security\IAuthorizator::ALL);
				} else {
					foreach ($roleData['resources'] as $resource => $actions) {
						if ($permission->hasResource($resource)) {
							$permission->allow($role, $resource, $actions ?: \Nette\Security\IAuthorizator::ALL);
						}
					}
				}
			}

			if (isset($roleData['denyResources']) && \is_array($roleData['denyResources'])) {
				if (!$roleData['denyResources']) {
					$permission->deny($role, \Nette\Security\IAuthorizator::ALL, \Nette\Security\IAuthorizator::ALL);
				} else {
					foreach ($roleData['denyResources'] as $resource => $actions) {
						if ($permission->hasResource($resource)) {
							$permission->deny($role, $resource, $actions ?: \Nette\Security\IAuthorizator::ALL);
						}
					}
				}
			}
		}
	}
}
