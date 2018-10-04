<?php

declare(strict_types=1);

namespace App\UserModule\Model;

final class UserService extends \IIS\Model\BaseService implements \Nette\Security\IAuthenticator
{
	public const USER_TYPE_ADMIN = 'admin',
		USER_TYPE_EMPLOYEE = 'zamestnanec',
		USER_TYPE_CLIENT = 'klient';


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
		$user = $this->getTable()->where('email', $email)->fetch();

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


	/**
	 * @throws \Nette\InvalidArgumentException
	 * @throws Exception
	 */
	public function registrateClient(\Nette\Utils\ArrayHash $data): void
	{
		$errorMessage = 'Při registraci nastala chyba.';
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
				'typ' => self::USER_TYPE_CLIENT,
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
}
