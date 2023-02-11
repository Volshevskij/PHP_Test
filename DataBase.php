<?php

declare(strict_types=1);

require_once('classes.php');

use Employee;

class DataBase implements HandlingInterface
{
    private const DATABASE_CONNECTION_ERROR_MESSAGE = 'Connection failed: ';
    private const DATABASE_CREATION_ERROR_MESSAGE = 'Error creating database: ';
    private const INVALID_ID_ERROR_MESSAGE = 'Emplyoee ID was less than 0';
    private const SAVE_EMPLOYEE_ERROR_MESSAGE = 'Employee data is not set';
    private const EMPLOYEE_DELETION_ERROR_MESSAGE = 'Error deleting user with ID: ';

    /**
     * @var mysqli
     */
    private mysqli $conn;

    /**
     * @var string
     */
    private string $dbname;
    
    /**
     * Class constructor
     *
     * @param mysqli $conn
     * @param string $dbname
     */
    public function __construct(mysqli $conn, string $dbname)
    {
        if (!class_exists('Employee.php')) {
            throw new Exception(self::CLASS_IS_NOT_EXIST_ERROR_MESSAGE);
        }

        if (!$conn || empty($dbname)) {
            throw new Exception(self::DATABASE_CONNECTION_ERROR_MESSAGE . mysqli_connect_error());
        }

        $this->dbname = $dbname;
        $this->conn = $conn;
        $this->checkDatabase();
    }

    /**
     * Checks if database exists and if not creates it
     *
     * @return void
     */
    private function checkDatabase(): void
    {
        $query = "CREATE DATABASE IF NOT EXISTS $this->dbname";
        if (!mysqli_query($this->conn, $query)) {
            throw new Exception(
                self::DATABASE_CREATION_ERROR_MESSAGE 
                . mysqli_error($this->conn) . '\n'
            );
        }
    }

    /**
     * Gets employee data from database by ID and returns it as array
     *
     * @param integer $id
     * @return array
     */
    public function getEmployeeById(int $id): array
    {
        if ($id < 0) {
            throw new Exception(self::INVALID_ID_ERROR_MESSAGE);
        }

        $sql = "SELECT * FROM employees WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt, 
            $ids, 
            $name, 
            $lastName,
            $birthDate, 
            $gender, 
            $birthCity
        );
        while (mysqli_stmt_fetch($stmt)) {
            $employees = array(
                'id' => $ids, 
                'name' => $name, 
                'lastName' => $lastName,
                'birthDate' => $birthDate,
                'gender' => $gender,
                'birthCity' => $birthCity
            );
        }
        mysqli_stmt_close($stmt);

        return $employees;
    }


    /**
     * Saves employee to database and returns its ID
     *
     * @param Employee $employee
     * @return integer
     */
    public function saveEmployee(Employee $employee): int
    {
        $data = $employee->getEmployeeDataAsArray();
        if (!isset($data)) {
            throw new Exception(self::SAVE_EMPLOYEE_ERROR_MESSAGE);
        }
        $sql = "INSERT INTO employees (name, lastName, birthDate, gender, birthCity) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        $formattedBirthDate = $data['birthDate']->format('Y-m-d H:i:s');
        mysqli_stmt_bind_param(
            $stmt, 
            "sssis",
             $data['name'], 
             $data['lastName'], 
             $formattedBirthDate, 
             $data['gender'], 
             $data['birthCity']
            );
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($this->conn);
        mysqli_stmt_close($stmt);

        return $id;
    }

    /**
     * Deletes employee from database and returns true on success
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteEmployeeById(int $id): bool
    {
        $result = false;
        if ($id < 0) {
            throw new Exception(self::INVALID_ID_ERROR_MESSAGE);
        }
        $sql = "DELETE FROM employees WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($affected_rows > 0) {
            $result =  true;
        } else {
            throw new Exception(self::EMPLOYEE_DELETION_ERROR_MESSAGE . $id);
        }

        return $result;
    }

    /**
     * Returns employees array by operator and number
     *
     * @param integer $number
     * @param string $operator
     * @return array
     */
    public function getEmployeeIdsByOperator(int $number, string $operator): array
    {
        $sql = "SELECT * FROM employees WHERE id $operator ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id);
        while (mysqli_stmt_fetch($stmt)) {
            $ids = array('id' => $id);
        }
        mysqli_stmt_close($stmt);

        return $ids;
    }
}
