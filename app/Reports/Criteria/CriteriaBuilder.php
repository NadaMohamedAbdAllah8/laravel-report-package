<?php

namespace App\Reports\Criteria;

use App\Reports\Criteria\Order\OrderCriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CriteriaBuilder
{
    protected Collection $filters;

    protected Collection $order;

    public function __construct()
    {
        $this->filters = collect([]);
        $this->order = collect([]);
    }
    
    public function where(string $field, string $operator, mixed $value): self
    {
        $this->filters->push(compact('field', 'operator', 'value'));
        return $this;
    }

    public function order(OrderCriteria $order): self
    {
        $this->order->push($order);
        return $this;
    }

    public function apply(Builder $query): Builder
    {
        $this->applyFilters($query);
        $this->applyOrders($query);

        return $query;
    }

    private function applyFilters(Builder $query): void
    {
        $this->filters->each(
            fn($filter): Builder =>
            $query->where($filter['field'], $filter['operator'], $filter['value'])
        );
    }

    private function applyOrders(Builder $query): void
    {
        $this->order->each(fn($order): Builder => $order->apply($query));
    }
}
