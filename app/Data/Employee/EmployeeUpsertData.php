<?php

namespace App\Data\Employee;

use App\Data\BaseData;

class EmployeeUpsertData extends BaseData
{
    public function __construct(
        public ?string $name = null,
        public ?string $address = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?int $department_id = null,
        public ?int $manager_id = null,
        public ?float $salary = null,
        public ?string $title = null,
    ) {}
}
