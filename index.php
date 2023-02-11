<?php

declare(strict_types=1);
error_reporting(E_ERROR | E_PARSE);

require_once('classes.php');

use Employee;
use DataBase;
use EmployeeListProcessor;

$host = 'localhost';
$user = 'root';
$password = '12344321';
$dbname = 'test_db';
$classDoesNotExist = 'One or more required classes does not exist';

if (
    !class_exists('Employee') 
    || !class_exists('DataBase')
    || !class_exists('EmployeeListProcessor')
) {
    throw new Exception($classDoesNotExist);
}

$conn = mysqli_connect(
    $host, 
    $user, 
    $password, 
    $dbname
);

$employeeData = [
	'name' => 'test',
	'lastName' => 'lnTest',
	'birthDate' => new DateTime(),
	'gender' => 0,
	'birthCity' => 'testCity',
];

$employeeData2 = [
	'name' => 'test2',
	'lastName' => 'lnTest2',
	'birthDate' => new DateTime(),
	'gender' => 0,
	'birthCity' => 'testCity2',
];

$employeeData3 = [
	'name' => 'test3',
	'lastName' => 'lnTest3',
	'birthDate' => new DateTime(),
	'gender' => 1,
	'birthCity' => 'testCity3',
];

$dataBase = new DataBase($conn, $dbname);
$employee = new Employee($dataBase, null, $employeeData);
$employee2 = new Employee($dataBase, null, $employeeData2);
$employee3 = new Employee($dataBase, 1);

$employee3->deleteEmployeeFromDataBase(1);
$employee3->saveEmployeeToDataBase();
echo Employee::convertBinaryToGender($employee3->getGender());
echo Employee::convertBirthDateToAge($employee3->getBirthDate());

echo $employee3->formatEmployee($employee2, true)->birthDate;

$employeeListProcessor = new EmployeeListProcessor(5, '!=', $dataBase);

echo $employeeListProcessor->getEmployeesByIdLsit();
echo $employeeListProcessor->deleteEmployeesByIdLsit();

mysqli_close($conn);