<?php

namespace App\Reports\Criteria\Order;

use App\Enums\SortingType;
use App\Reports\Criteria\Criteria;
use Illuminate\Database\Eloquent\Builder;
class OrderBySqlExpression implements Criteria
{
    protected string $expression;
    protected string $direction;

    public function __construct(string $expression, string $direction =SortingType::ASC->value)
    {
        $this->expression = $expression;
        $this->direction = $direction;
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderByRaw("{$this->expression} {$this->direction}");
    }
}
