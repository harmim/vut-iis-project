<?php

declare(strict_types=1);

namespace App\UserModule\Model;

final class RecordService extends \IIS\Model\BaseService
{

    public function getCostumeTable(): \Nette\Database\Table\Selection
    {
    	return $this->database->table('kostym');
    }

    public function getAcessoryTable(): \Nette\Database\Table\Selection
    {
    	return $this->database->table('doplnek');
    }

    public function getRecordTable(): \Nette\Database\Table\Selection
    {
        return $this->database->table('zaznam');
    }

    public function getClientTable(): \Nette\Database\Table\Selection
    {
        return $this->database->table('klient');
    }

    public function getEmployeeTable(): \Nette\Database\Table\Selection
    {
        return $this->database->table('zamestnanec');
    }

    public function getUserTable(): \Nette\Database\Table\Selection
    {
       return $this->database->table('uzivatel');
    }

	public function getTableName(): string
	{
		return 'zaznam';
	}

	public function readCostume(int $id)
	{
	    return $this->getCostumeTable()->get($id);
	}

	public function readAcessory(int $id)
	{
    	   return $this->getAcessoryTable()->get($id);
    }

    public function readClient(int $id)
    {
        return $this->getClientTable()->get($id);
    }

    public function readEmployee(int $id)
    {
        return $this->getEmployeeTable()->get($id);
    }

    public function readUser(int $id)
    {
        return $this->getUserTable()->get($id);
    }

    public function getEmployeeIdByUserId(int $userId)
    {
        return $this->readUser($userId)->zamestnanec_id;
    }

    /**
     * @throws \App\UserModule\Model\Exception
    * @throws \Nette\InvalidArgumentException
    */
    public function closeRecord(\Nette\Utils\ArrayHash $data): void
    {
    	try {
    		$updateData = [
    			'datum_vraceni' => $data->datum_vraceni,
    		];
    		$this->getRecordTable()->wherePrimary($data->id)->update($updateData);
    	} catch (\Nette\Database\UniqueConstraintViolationException $e) {
    		throw new \App\UserModule\Model\Exception('chyba');
    	}
    }


    /**
         * @throws \App\UserModule\Model\Exception
        * @throws \Nette\InvalidArgumentException
        */
        public function confirmReservation(\Nette\Utils\ArrayHash $data): void
        {

        	try {
                $employeeId = $this->getEmployeeIdByUserId(intval($data->employee_id));
        		$updateData = [
        			'zamestnanec_id' => $employeeId,
        		];
        		$this->getRecordTable()->wherePrimary($data->record_id)->update($updateData);
        	} catch (\Nette\Database\UniqueConstraintViolationException $e) {
        		throw new \App\UserModule\Model\Exception('chyba');
        	}
        }

}