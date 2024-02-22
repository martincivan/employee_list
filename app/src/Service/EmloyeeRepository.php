<?php

namespace App\Service;

use App\Entity\Employee;

interface EmloyeeRepository
{

    /**
     * @return Employee[]
     */
    public function findAll(): array;
    public function find(string $id): ?Employee;
    public function create(Employee $employee): string;
    public function update(Employee $employee): bool;

    public function delete(string $id): bool;

}
