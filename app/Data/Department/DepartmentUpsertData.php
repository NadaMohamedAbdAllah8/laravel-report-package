<?php

namespace App\Data\Department;

use App\Data\BaseData;

class DepartmentUpsertData extends BaseData
{
    public function __construct(
        public ?string $name = null,
    ) {}
}
