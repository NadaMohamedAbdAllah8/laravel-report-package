<?php

namespace Nada\ReportBuilder\Reports\Criteria;

use Illuminate\Database\Eloquent\Builder;

class Filter implements Criteria
{
    public function __construct(
        protected string $field,
        protected string $operator,
        protected mixed $value
    ) {}

    public function apply(Builder $query): Builder
    {
        return $query->where($this->field, $this->operator, $this->value);
    }
}
