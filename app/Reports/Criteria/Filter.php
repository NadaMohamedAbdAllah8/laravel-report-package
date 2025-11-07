<?php

namespace App\Reports\Criteria;

use Illuminate\Database\Eloquent\Builder;

class Filter implements Criteria
{
    protected string $field;
    protected string $operator;
    protected mixed $value;

    public function __construct(string $field, string $operator, mixed $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function apply(Builder $query): Builder
    {
        return  $query->where($this->field, $this->operator, $this->value);
    }
}
