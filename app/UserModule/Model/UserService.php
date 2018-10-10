<?php

declare(strict_types=1);

namespace App\UserModule\Model;

final class UserService extends \IIS\Model\BaseService implements \Nette\Security\IAuthenticator
{
	public const ROLE_TRANSLATION_MAP = [
		\App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN => 'Administrátor',
		\App\UserModule\Model\AuthorizatorFactory::ROLE_EMPLOYEE => 'Zaměstnanec',
		\App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT => 'Klient',
	];


	public function getTableName(): string
	{
		return 'uzivatel';
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials): \Nette\Security\IIdentity
	{
		[self::USERNAME => $email, self::PASSWORD => $password] = $credentials;
		$user = $this->getTable()
			->where('email', $email)
			->where('aktivni', true)
			->fetch();

		$errorMessage = 'Zadané přihlašovací údaje jsou chybné.';
		if (!$user) {
			throw new \Nette\Security\AuthenticationException($errorMessage, self::IDENTITY_NOT_FOUND);

		} elseif (!\Nette\Security\Passwords::verify($password, $user->heslo)) {
			throw new \Nette\Security\AuthenticationException($errorMessage, self::INVALID_CREDENTIAL);

		} elseif (\Nette\Security\Passwords::needsRehash($user->heslo)) {
			$user->update([
				'heslo' => \Nette\Security\Passwords::hash($password),
			]);
		}

		$userData = $user->toArray();
		unset($userData['password']); // return user without password due to security reasons

		return new \Nette\Security\Identity($user->id, [$user->typ], $userData);
	}


	public function getClientTable(): \Nette\Database\Table\Selection
	{
		return $this->database->table('klient');
	}


	public function getCompanyTable(): \Nette\Database\Table\Selection
	{
		return $this->database->table('pravnicka_osoba');
	}


	public function getEmployeeTable(): \Nette\Database\Table\Selection
	{
		return $this->database->table('zamestnanec');
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \App\UserModule\Model\Exception
	 */
	public function registrateClient(\Nette\Utils\ArrayHash $data): void
	{
		$errorMessage = 'Při vytváření účtu nastala chyba.';
		$this->database->beginTransaction();

		$client = $this->getClientTable()->insert([
			'jmeno' => $data->firstName,
			'prijmeni' => $data->lastName,
			'datum_narozeni' => $data->bornDate,
			'telefonni_cislo' => $data->phone,
			'adresa' => $data->address,
		]);
		if (!$client instanceof \Nette\Database\Table\ActiveRow) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception($errorMessage);
		}

		if ($data->useCompany === true) {
			$company = $this->getCompanyTable()->insert([
				'klient_id' => $client->id,
				'ico' => $data->ico,
				'dic' => $data->dic,
				'fakturacni_adresa' => $data->companyAddress,
			]);
			if (!$company instanceof \Nette\Database\Table\ActiveRow) {
				$this->database->rollBack();
				throw new \App\UserModule\Model\Exception($errorMessage);
			}
		}

		try {
			$user = $this->getTable()->insert([
				'typ' => \App\UserModule\Model\AuthorizatorFactory::ROLE_CLIENT,
				'email' => $data->email,
				'heslo' => \Nette\Security\Passwords::hash($data->password),
				'klient_id' => $client->id,
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}
		if (!$user instanceof \Nette\Database\Table\ActiveRow) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception($errorMessage);
		}

		$this->database->commit();
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws \App\UserModule\Model\Exception
	 */
	public function addAdmin(\Nette\Utils\ArrayHash $data): void
	{
		try {
			$user = $this->getTable()->insert([
				'typ' => \App\UserModule\Model\AuthorizatorFactory::ROLE_ADMIN,
				'email' => $data->email,
				'heslo' => \Nette\Security\Passwords::hash($data->password),
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}

		if (!$user instanceof \Nette\Database\Table\ActiveRow) {
			throw new \App\UserModule\Model\Exception('Administrátora se nepodařilo přidat.');
		}
	}


	/**
	 * @throws \App\UserModule\Model\Exception
	 * @throws \Nette\InvalidArgumentException
	 */
	public function addEmployee(\Nette\Utils\ArrayHash $data): void
	{
		$errorMessage = 'Zaměstnance se nepodařilo přidat.';
		$this->database->beginTransaction();

		$employee = $this->getEmployeeTable()->insert([
			'jmeno' => $data->firstName,
			'prijmeni' => $data->lastName,
			'datum_narozeni' => $data->bornDate,
			'telefonni_cislo' => $data->phone,
		]);
		if (!$employee instanceof \Nette\Database\Table\ActiveRow) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception($errorMessage);
		}

		try {
			$user = $this->getTable()->insert([
				'typ' => \App\UserModule\Model\AuthorizatorFactory::ROLE_EMPLOYEE,
				'email' => $data->email,
				'heslo' => \Nette\Security\Passwords::hash($data->password),
				'zamestnanec_id' => $employee->id,
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}
		if (!$user instanceof \Nette\Database\Table\ActiveRow) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception($errorMessage);
		}

		$this->database->commit();
	}


	/**
	 * @throws \App\UserModule\Model\Exception
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editAdmin(\Nette\Utils\ArrayHash $data): void
	{
		try {
			$updateData = [
				'email' => $data->email,
			];
			if ($data->password) {
				$updateData['heslo'] = \Nette\Security\Passwords::hash($data->password);
			}
			$this->getTable()->wherePrimary($data->id)->update($updateData);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}
	}


	/**
	 * @throws \App\UserModule\Model\Exception
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editEmployee(\Nette\Utils\ArrayHash $data): void
	{
		$this->database->beginTransaction();

		$this->getEmployeeTable()->wherePrimary($data->employeeId)->update([
			'jmeno' => $data->firstName,
			'prijmeni' => $data->lastName,
			'datum_narozeni' => $data->bornDate,
			'telefonni_cislo' => $data->phone,
		]);

		try {
			$updateData = [
				'email' => $data->email,
			];
			if ($data->password) {
				$updateData['heslo'] = \Nette\Security\Passwords::hash($data->password);
			}
			$this->getTable()->wherePrimary($data->id)->update($updateData);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}

		$this->database->commit();
	}


	/**
	 * @throws \App\UserModule\Model\Exception
	 * @throws \Nette\InvalidArgumentException
	 */
	public function editClient(\Nette\Utils\ArrayHash $data): void
	{
		$this->database->beginTransaction();

		$this->getClientTable()->wherePrimary($data->clientId)->update([
			'jmeno' => $data->firstName,
			'prijmeni' => $data->lastName,
			'datum_narozeni' => $data->bornDate,
			'telefonni_cislo' => $data->phone,
			'adresa' => $data->address,
		]);

		if ($data->useCompany === true) {
			$companyData = [
				'klient_id' => $data->clientId,
				'ico' => $data->ico,
				'dic' => $data->dic,
				'fakturacni_adresa' => $data->companyAddress,
			];
			$this->database->query(
				'INSERT INTO `pravnicka_osoba`',
				$companyData,
				'ON DUPLICATE KEY UPDATE',
				$companyData
			);
		} else {
			$this->getCompanyTable()->wherePrimary($data->clientId)->delete();
		}

		try {
			$updateData = [
				'email' => $data->email,
			];
			if ($data->password) {
				$updateData['heslo'] = \Nette\Security\Passwords::hash($data->password);
			}
			$this->getTable()->wherePrimary($data->id)->update($updateData);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			$this->database->rollBack();
			throw new \App\UserModule\Model\Exception('Uživatel s tímto e-mailem již existuje.');
		}

		$this->database->commit();
	}


	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function changeActive(int $id, bool $active): void
	{
		$this->getTable()->wherePrimary($id)->update([
			'aktivni' => $active,
		]);
	}
}
