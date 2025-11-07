<?php

namespace App\Reports;

use App\Exceptions\ValidationException;
use App\Validators\Reports\ReportBuilderValidator;
use Illuminate\Support\Facades\Log;

class UnpaginatedReportBuilder extends BaseReportBuilder
{
    protected $expressions;

    protected $expressionsValues;

    protected $sortCollectionAttributes;

    /**
     * Adds an expression that will be applied to the entire collection
     *
     * @throws ValidationException
     */
    public function expression($key, $lambda_function): BaseReportBuilder
    {
        if (!is_callable($lambda_function)) {
            Log::error('[ReportBuilder] Invalid function provided for expression', ['key' => $key]);
            throw new ValidationException('Not a valid function!');
        }

        $this->expressions[$key] = $lambda_function;

        return $this;
    }

    private function applyExpression($item, $carry, $expression, $param_names): mixed
    {
        $args = [];
        foreach ($param_names as $param) {
            $args[] = $item->$param;
        }

        return $carry + $expression(...$args);
    }

    private function buildExpression($collection, $expression, $param_names)
    {
        return $collection->reduce(function ($carry, $item) use ($expression, $param_names) {
            return $this->applyExpression($item, $carry, $expression, $param_names);
        });
    }

    private function buildExpressions(): BaseReportBuilder
    {
        foreach ($this->expressions as $key => $expression) {
            $reflection = $this->getReflection($expression);
            $param_names = $this->getParametersNames($reflection);

            $collection = $this->collection;
            $expression_value = $this->buildExpression(collection: $collection, expression: $expression, param_names: $param_names);

            $this->expressionsValues[$key] = $expression_value;
        }

        return $this;
    }

    protected function buildSorts(): static
    {
        $items = $this->getItemsKeys();
        ReportBuilderValidator::throwExceptionIfAttributeNotExist(
            attributes: $this->sortCollectionAttributes,
            items: $items
        );

        $this->collection = $this->collection->sortBy($this->sortCollectionAttributes)->values();

        return $this;
    }

    public function build(): BaseReportBuilder
    {
        $this->applyCriteria();
        $this->buildAttributes();
        $this->buildRelationAttributes();
        $this->buildDerivedAttributes();
        $this->buildSorts();
        $this->buildExpressions();

        return $this;
    }
}
