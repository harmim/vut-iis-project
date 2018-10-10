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
		foreach (\Nette\Utils\Finder::findDirectories('*Module')->in($appDir) as $dir) {
			/** @var \SplFileInfo $dir */
			\preg_match('~^(.+)Module\z~', $dir->getFilename(), $matches);
			$permission->addResource(\strtolower($matches[1]));
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
							$permission->allow($role, $resource, $actions);
						}
					}
				}
			}

			if (isset($roleData['denyResources']) && \is_array($roleData['denyResources'])) {
				foreach ($roleData['denyResources'] as $resource => $actions) {
					if ($permission->hasResource($resource)) {
						$permission->deny($role, $resource, $actions);
					}
				}
			}
		}
	}
}
