<?php

namespace Nada\ReportBuilder\Reports\Criteria\Order;

use Illuminate\Database\Eloquent\Builder;
use Nada\ReportBuilder\Reports\BaseReportBuilder;

class OrderByColumn implements OrderCriteria
{
    public function __construct(
        protected string $column,
        protected string $direction = BaseReportBuilder::SORT_ASC
    ) {}

    public function apply(Builder $query): Builder
    {
        return $query->orderBy($this->column, $this->direction);
    }
}
