<?php

namespace Nada\ReportBuilder\Reports\Criteria\Order;

use Illuminate\Database\Eloquent\Builder;
use Nada\ReportBuilder\Reports\BaseReportBuilder;

class OrderBySqlExpression implements OrderCriteria
{
    public function __construct(
        protected string $expression,
        protected string $direction = BaseReportBuilder::SORT_ASC
    ) {}

    public function apply(Builder $query): Builder
    {
        return $query->orderByRaw("{$this->expression} {$this->direction}");
    }
}
