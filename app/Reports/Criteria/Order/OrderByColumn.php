<?php
namespace App\Reports\Criteria\Order;

use App\Enums\SortingType;
use App\Reports\Criteria\Criteria;
use Illuminate\Database\Eloquent\Builder;

class OrderByColumn implements Criteria
{
    protected string $column;
    protected string $direction;

    public function __construct(string $column, string $direction=SortingType::ASC->value)
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderBy($this->column, $this->direction);
    }
}
