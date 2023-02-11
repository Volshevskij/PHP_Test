<?php

declare(strict_types=1);

require_once('classes.php');

use Employee;
use DataBase;
use HandlingInterface;

class EmployeeListProcessor implements HandlingInterface
{
    private const UNSOPPORTED_OPERATOR_ERROR_MESSAGE = 'Provided operator is not supported';
    private const ID_LIST_EMPTY_ERROR_MESSAGE = 'Id list is empty';

    /**
     * @var array
     */
    private array $employeeIds;

    /**
     * @var DataBase
     */
    private DataBase $dataBase;

    /**
     * Class constructor
     *
     * @param integer $number
     * @param string $operator
     * @param DataBase $dataBase
     */
    public function __construct(int $number, string $operator, DataBase $dataBase)
    {
        if (!class_exists('Employee.php') || !class_exists('DataBase.php')) {
            throw new Exception(self::CLASS_IS_NOT_EXIST_ERROR_MESSAGE);
        }

        $this->dataBase = $dataBase;
        $this->employeeIds = $this->findEmployeesByOperator($number, $operator);
    }

    /**
     * Returns employees array by operator and number
     *
     * @param integer $number
     * @param string $operator
     * @return array
     */
    private function findEmployeesByOperator(int $number, string $operator): array
    {
        if (
            $operator !== '<'
            && $operator !== '>'
            && $operator !== '!='
        ) {
            throw new Exception(self::UNSOPPORTED_OPERATOR_ERROR_MESSAGE);
        }

        return $this->dataBase->getEmployeeIdsByOperator($number, $operator);
    }

    /**
     * Returns employees array by ID list
     *
     * @return array
     */
    public function getEmployeesByIdLsit(): array
    {
        if (!isset($this->employeeIds)) {
            throw new Exception(self::ID_LIST_EMPTY_ERROR_MESSAGE);
        }

        $resultArray = [];
        foreach ($this->employeeIds as $id) {
            $data = $this->dataBase->getEmployeeById($id)[0];
            if (isset($data)) {
                $resultArray[] = $data;
            }
        }

        return $resultArray;
    }

    /**
     * Deletes employees from database by ID list
     *
     * @return boolean
     */
    public function deleteEmployeesByIdLsit(): bool
    {
        if (!isset($this->employeeIds)) {
            throw new Exception(self::ID_LIST_EMPTY_ERROR_MESSAGE);
        }

        foreach ($this->employeeIds as $id) {
            $this->dataBase->deleteEmployeeById($id);
        }

        return true;
    }
}