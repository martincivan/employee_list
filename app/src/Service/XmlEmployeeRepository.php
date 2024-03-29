<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Gender;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use DOMXPath;
use RuntimeException;

class XmlEmployeeRepository implements EmloyeeRepository
{

    const DB_FILE_ENV_VAR = 'DB_FILE';
    const EMPLOYEES = 'employees';
    const EMPLOYEE = 'employee';
    const EMPLOYEE_ID = 'id';
    const EMPLOYEE_NAME = 'name';
    const EMPLOYEE_BIRTH_DATE = 'birth_date';
    const EMPLOYEE_GENDER = 'gender';
    const GENDER_MALE = "M";
    const GENDER_FEMALE = "F";

    public function findAll(): array
    {
        $employees = [];
        $document = $this->load();
        $employeeData = $document->getElementsByTagName(self::EMPLOYEE);
        foreach ($employeeData as $employeeItem) {
            $employees[] = $this->getEntity($employeeItem);
        }

        return $employees;
    }

    private function load(): DOMDocument
    {
        if (!file_exists($this->getFilePath())) {
            $xmlDoc = new DOMDocument();
            $element = $xmlDoc->createElement(self::EMPLOYEES);
            $xmlDoc->appendChild($element);
            $this->save($xmlDoc);
        }
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($this->getFilePath());
        return $xmlDoc;
    }

    private function getFilePath(): string
    {
        return $_ENV[self::DB_FILE_ENV_VAR];
    }

    private function save(DOMDocument $document)
    {
        $document->save($this->getFilePath());
    }

    public function find(string $id): ?Employee
    {
        $document = $this->load();
        $employeeItem = $this->findNode($document, $id);
        if ($employeeItem === null) {
            return null;
        }

        return $this->getEntity($employeeItem);
    }

    public function create(Employee $employee): string
    {
        if ($employee->getId() === null) {
            $employee->setId(uniqid(more_entropy: true));
        }

        $document = $this->load();
        $employees = $document->getElementsByTagName(self::EMPLOYEES)[0]; //TODO: validate there aren't more than one employees element
        if ($employees === null) {
            throw new RuntimeException("Invalid XML structure.");
        }
        $employeeElement = $document->createElement(self::EMPLOYEE);
        $employeeElement->setAttribute(self::EMPLOYEE_ID, $employee->getId());
        $employeeElement->setAttribute(self::EMPLOYEE_NAME, $employee->getName());
        $employeeElement->setAttribute(self::EMPLOYEE_BIRTH_DATE, $employee->getBirthDate()->getTimestamp());
        $employeeElement->setAttribute(self::EMPLOYEE_GENDER, match($employee->getGender()) {
           Gender::MALE => self::GENDER_MALE,
           Gender::FEMALE => self::GENDER_FEMALE,
        });

        $employees->appendChild($employeeElement);
        $this->save($document);

        return $employee->getId();
    }

    public function update(Employee $employee): bool
    {
        if ($employee->getId() === null) {
            return false;
        }
        $document = $this->load();
        $employeeItem = $this->findNode($document, $employee->getId());
        if ($employeeItem === null) {
            return false;
        }
        $employeeItem->setAttribute(self::EMPLOYEE_NAME, $employee->getName());
        $employeeItem->setAttribute(self::EMPLOYEE_BIRTH_DATE, $employee->getBirthDate()->getTimestamp());
        $employeeItem->setAttribute(self::EMPLOYEE_GENDER, match($employee->getGender()) {
            Gender::MALE => self::GENDER_MALE,
            Gender::FEMALE => self::GENDER_FEMALE,
        });
        $this->save($document);
        return true;
    }

    public function delete(string $id): bool
    {
        $document = $this->load();
        $employeeItem = $this->findNode($document, $id);
        if ($employeeItem === null) {
            return false;
        }
        $employees = $document->getElementsByTagName(self::EMPLOYEES)[0]; //TODO: validate there aren't more than one employees element
        if ($employees === null) {
            throw new RuntimeException("Invalid XML structure.");
        }
        $employees->removeChild($employeeItem);
        $this->save($document);
        return true;
    }

    private function findNode(DOMDocument $document, string $id): ?DOMNode {
        $xpath = new DOMXPath($document);
        $query = "//" . self::EMPLOYEE . "[@" . self::EMPLOYEE_ID . "='$id']";
        $employeeNodes = $xpath->query($query);
        if ($employeeNodes->length === 0 || ($employeeItem = $employeeNodes->item(0)) === null) { //TODO: validate there aren't more than one employees element
            return null;
        }
        return $employeeItem;
    }

    /**
     * @param mixed $employeeItem
     * @return Employee
     * @throws \Exception
     */
    public function getEntity(DOMNode $employeeItem): Employee
    {
        $employee = new Employee();
        $employee->setId($employeeItem->getAttribute(self::EMPLOYEE_ID));
        $employee->setName($employeeItem->getAttribute(self::EMPLOYEE_NAME));
        $employee->setBirthDate(new DateTimeImmutable('@' . $employeeItem->getAttribute(self::EMPLOYEE_BIRTH_DATE)));
        $gender = $employeeItem->getAttribute(self::EMPLOYEE_GENDER);
        if ($gender === self::GENDER_MALE) {
            $employee->setGender(Gender::MALE);
        }
        if ($gender === self::GENDER_FEMALE) {
            $employee->setGender(Gender::FEMALE);
        }
        return $employee;
    }
}
