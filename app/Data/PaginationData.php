<?php

namespace App\Data;

class PaginationData extends BaseData
{
    public function __construct(
        public ?int $per_page = 15,
        public ?int $page = 1,
    ) {}
}
