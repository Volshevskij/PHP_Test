<?php

declare(strict_types=1);

require_once('classes.php');

use DataBase;

class Employee implements HandlingInterface
{
	private const SAVE_ERROR_MESSAGE = 'Can\'t save entity to data base';
	private const DELETE_ERROR_MESSAGE = 'Can\'t delete entity from data base';
	private const BINARY_INPUT_ERROR_MESSAGE = 'Invalid gender binary input';
	private const EMPTY_FIELDS_ERROR_MESSAGE = 'Cant\'t set data fields to employee: data fields are empty';

	/**
	 * @var integer
	 */
	private int $id;

	/**
	 * @var string
	 */
	private string $name;

	/**
	 * @var string
	 */
	private string $lastName;

	/**
	 * @var DateTime
	 */
	private DateTime $birthDate;

	/**
	 * @var integer
	 */
	private int $gender;

	/**
	 * @var string
	 */
	private string $birthCity;

	/**
	 * @var DataBase
	 */
	private DataBase $dataBase;

	/**
	 * Class constructor
	 *
	 * @param DataBase $dataBase
	 * @param integer|null $id
	 * @param array|null $dataFields
	 */
	public function __construct(
		DataBase $dataBase, 
		int $id = null, 
		array $dataFields = null
	) {
		if (!class_exists('DataBase.php')) {
            throw new Exception(self::CLASS_IS_NOT_EXIST_ERROR_MESSAGE);
        }

		$this->dataBase = $dataBase;

		if ($id !== null) {
			$fields = $this->dataBase->getEmployeeById($id);
			$this->setAllFields($fields);
		} else {
			$this->setAllFields($dataFields);
			$this->saveEmployeeToDataBase();
		}
	}

	/**
	 * Returns id number
	 *
	 * @return integer
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Sets id number
	 *
	 * @param integer $id
	 * @return Employee
	 */
	public function setId(int $id): Employee
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Returns name stirng
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
	
	/**
	 * Sets name string
	 *
	 * @param string $name
	 * @return Employee
	 */
	public function setName(string $name): Employee
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Returns last name string
	 *
	 * @return string
	 */
	public function getLastName(): string
	{
		return $this->lastName;
	}

	/**
	 * Sets last name string
	 *
	 * @param string $lastName
	 * @return Employee
	 */
	public function setLastName(string $lastName): Employee
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * Returns date of birth
	 *
	 * @return DateTime
	 */
	public function getBirthDate(): DateTime
	{
		return $this->birthDate;
	}

	/**
	 * Sets date of birth
	 *
	 * @param DateTime $birthDate
	 * @return Employee
	 */
	public function setBirthDate(DateTime $birthDate): Employee
	{
		$this->birthDate = $birthDate;
		return $this;
	}

	/**
	 * Returns gender number
	 *
	 * @return integer
	 */
	public function getGender(): int
	{
		return $this->gender;
	}

	/**
	 * Sets gender number
	 *
	 * @param integer $gender
	 * @return Employee
	 */
	public function setGender(int $gender): Employee
	{
		$this->gender = $gender;
		return $this;
	}

	/**
	 * Returns birth city string
	 *
	 * @return string
	 */
	public function getBirthCity(): string
	{
		return $this->birthCity;
	}

	/**
	 * Sets birth city string
	 *
	 * @param string $birthCity
	 * @return Employee
	 */
	public function setBirthCity(string $birthCity): Employee
	{
		$this->birthCity = $birthCity;
		return $this;
	}

	/**
	 * Saves current employee to database
	 *
	 * @return bool
	 */
	public function saveEmployeeToDataBase(): bool
	{
		$result = false;
		try {
			$result = (bool)$this->dataBase->saveEmployee($this);
		} catch (Exception $e) {
			throw new Exception(self::SAVE_ERROR_MESSAGE);
		}

		return $result;
	}

	/**
	 * Deletes employee from database by $id
	 *
	 * @param integer $id
	 * @return string
	 */
	public function deleteEmployeeFromDataBase(int $id): bool
	{
		$response = false;
		try {
			$response =	$this->dataBase->deleteEmployeeById($id);
		} catch (Exception $e) {
			throw new Exception(self::DELETE_ERROR_MESSAGE);
		}
		return $response;
	}

	/**
	 * Converts birth date to age number
	 *
	 * @param Employee $employee
	 * @return integer
	 */
	public static function convertBirthDateToAge(DateTime $date): int
	{
		$currentDate = new DateTime();

		return $currentDate->diff($date)->y;
	}

	/**
	 * Converts binary number to gender string
	 *
	 * @param integer $binary
	 * @return string
	 */
	public static function convertBinaryToGender(int $binary): string
	{
  		if ($binary === 0) {
    		return 'Female';
  		} else if ($binary === 1) {
    		return 'Male';
  		} else {
    		throw new Exception(self::BINARY_INPUT_ERROR_MESSAGE);
		}
    }

	/**
	 * Sets all fields of model
	 *
	 * @param array $fields
	 * @return void
	 */
	private function setAllFields(array $fields): void
	{
		if(!isset($fields)) {
			throw new Exception(self::EMPTY_FIELDS_ERROR_MESSAGE);
		}

		$this
			->setId($fields['id'])
			->setName($fields['name'])
			->setLastName($fields['lastName'])
			->setBirthDate($fields['birthDate'])
			->setGender($fields['gender'])
			->setBirthCity($fields['birthCity']);
	}

	/**
	 * Returns current employee data as array
	 *
	 * @return array
	 */
	public function getEmployeeDataAsArray(): array
	{
		return array(
			'id' => $this->id,
			'name' => $this->name,
			'lastName' => $this->lastName,
			'birthDate' => $this->birthDate,
			'gender' => $this->gender,
			'birthCity' => $this->birthCity,
		);
	}

	/**
	 * Returnes stdClass with optionally formated fields
	 *
	 * @param Employee $employee
	 * @param boolean $ageFormatting
	 * @param boolean $genderFormatting
	 * @return stdClass
	 */
	public function formatEmployee(
		Employee $employee, 
		bool $ageFormatting = false, 
		bool $genderFormatting = false
	): stdClass 
	{
		$data = $employee->getEmployeeDataAsArray();
		if ($ageFormatting === true) {
			$data['birthDate'] = Employee::convertBirthDateToAge($data['birthDate']);
		}

		if ($genderFormatting === true) {
			$data['gender'] = Employee::convertBinaryToGender($data['gender']);
		}

		return (object)$data;
	}
}