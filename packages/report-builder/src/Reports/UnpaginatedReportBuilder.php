<?php

namespace Nada\ReportBuilder\Reports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Nada\ReportBuilder\Exceptions\ReportBuilderException;
use Nada\ReportBuilder\Validators\Reports\ReportBuilderValidator;

class UnpaginatedReportBuilder extends BaseReportBuilder
{
    protected array $sortCollectionAttributes = [];

    /**
     * Adds an expression that will be applied to the entire collection
     *
     * @throws ValidationException
     */
    public function expression(string $key, callable $lambdaFunction): BaseReportBuilder
    {
        if (! is_callable($lambdaFunction)) {
            Log::error('[ReportBuilder] Invalid function provided for expression', ['key' => $key]);
            throw new ReportBuilderException('Not a valid function!');
        }

        $this->expressions[$key] = $lambdaFunction;

        return $this;
    }

    private function applyExpression(object $item, mixed $carry, callable $expression, array $paramNames): mixed
    {
        $args = [];
        foreach ($paramNames as $param) {
            $args[] = $item->$param;
        }

        return $carry + $expression(...$args);
    }

    private function buildExpression(Collection $collection, callable $expression, array $paramNames): mixed
    {
        return $collection->reduce(function (mixed $carry, object $item) use ($expression, $paramNames): mixed {
            return $this->applyExpression($item, $carry, $expression, $paramNames);
        });
    }

    private function buildExpressions(): BaseReportBuilder
    {
        foreach ($this->expressions as $key => $expression) {
            $reflection = $this->getReflection($expression);
            $paramNames = $this->getParametersNames($reflection);

            $collection = $this->collection;
            $expressionValue = $this->buildExpression(collection: $collection, expression: $expression, paramNames: $paramNames);

            $this->expressionsValues[$key] = $expressionValue;
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
