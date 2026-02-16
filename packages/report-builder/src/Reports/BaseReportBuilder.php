<?php

namespace Nada\ReportBuilder\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Nada\ReportBuilder\Exceptions\ReportBuilderException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

abstract class BaseReportBuilder
{
    public const SORT_ASC = 'asc';

    public const SORT_DESC = 'desc';

    protected Builder $query;

    protected Collection $collection;

    protected array $attributes;

    protected array $relationsAttributes;

    protected array $derivedAttributes;

    protected array $expressions;

    protected array $expressionsValues;

    protected Collection $criteria;

    public function __construct(Builder $query)
    {
        $this->query = $query;
        $this->attributes = [];
        $this->relationsAttributes = [];
        $this->expressions = [];
        $this->derivedAttributes = [];
        $this->expressionsValues = [];
        $this->criteria = collect([]);
    }

    /**
     * Adds a single of attribute that will be selected from the collection collection
     */
    public function attribute(string $attribute): BaseReportBuilder
    {
        array_push($this->attributes, $attribute);

        return $this;
    }

    /**
     * Adds an array of attributes that will be selected from the collection collection
     */
    public function attributes(array $attributes): BaseReportBuilder
    {
        array_push($this->attributes, $attributes);
        $this->attributes = Arr::flatten($this->attributes);

        return $this;
    }

    /**
     * Adds key and value of a single attribute that will be selected from a relation on the collection
     */
    public function relationAttribute(string $key, string $relationAttribute): BaseReportBuilder
    {
        $this->relationsAttributes[$key] = $relationAttribute;

        return $this;
    }

    /**
     * Adds an attribute that will be calculated for every single record of the collection collection
     *
     * @throws ValidationException
     */
    public function derivedAttribute(string $key, callable $lambdaFunction): BaseReportBuilder
    {
        if (! is_callable($lambdaFunction)) {
            Log::error(
                '[BaseReportBuilder] Invalid function provided for derived attribute',
                [
                    'key' => $key,
                ]
            );
            throw new ReportBuilderException('Not a valid function!');
        }
        $this->derivedAttributes[$key] = $lambdaFunction;

        return $this;
    }

    public function filterBetween(string $column, ?string $fromDate, ?string $toDate): BaseReportBuilder
    {
        if ($fromDate !== null && $toDate !== null) {
            $this->query->whereBetween($column, [$fromDate, $toDate]);
        }

        return $this;
    }

    abstract public function build(): BaseReportBuilder;

    /**
     * Gets the final result of the collection collection and expressions
     */
    public function get(): Collection
    {
        $finalCollection = collect([]);
        $finalCollection['data'] = $this->getItems();
        $finalCollection['expressionValues'] = $this->expressionsValues;

        Log::info(
            '[BaseReportBuilder] Successfully retrieved final collection',
            ['record_count' => count($finalCollection['data'])]
        );

        return $finalCollection;
    }

    protected function getReflection(callable $function): ReflectionFunctionAbstract
    {
        return new ReflectionFunction($function);
    }

    protected function getParametersNames(ReflectionFunctionAbstract $reflection): array
    {
        return array_map(
            function (ReflectionParameter $param): string {
                return $param->getName();
            },
            $reflection->getParameters()
        );
    }

    protected function buildDerivedAttributes(): BaseReportBuilder
    {
        foreach ($this->derivedAttributes as $key => $derivedAttribute) {
            $reflection = $this->getReflection($derivedAttribute);
            $paramNames = $this->getParametersNames($reflection);
            $this->buildDerivedAttribute(derivedAttribute: $derivedAttribute, paramNames: $paramNames, key: $key);
        }

        return $this;
    }

    protected function buildRelationAttributes(): BaseReportBuilder
    {
        foreach ($this->relationsAttributes as $key => $relationAttribute) {
            $this->buildRelationAttribute(key: $key, relationAttribute: $relationAttribute);
        }

        return $this;
    }

    protected function buildAttributes(): BaseReportBuilder
    {
        $this->collection = $this->query->get();

        return $this;
    }

    protected function applyCriteria(): BaseReportBuilder
    {
        $this->criteria->each(function ($criteria): void {
            $this->collection = $criteria->apply($this->collection);
        });

        return $this;
    }

    private function getRelationAttribute(object $model, string $relationAttribute): mixed
    {
        $lastDotPosition = strrpos($relationAttribute, '.');
        $relationName = substr($relationAttribute, 0, $lastDotPosition);
        $attribute = substr($relationAttribute, $lastDotPosition + 1);

        $relations = explode('.', $relationName);

        foreach ($relations as $relation) {
            if (isset($model->$relation)) {
                $model = $model->$relation;
            } else {
                return null;
            }
        }

        return $model->$attribute;
    }

    private function buildDerivedAttribute(callable $derivedAttribute, array $paramNames, string $key): Collection
    {
        return $this->collection = $this->collection
            ->map(
                function (object $item) use ($derivedAttribute, $paramNames, $key): object {
                    return $this->applyDerivedAttribute(
                        collection: $item,
                        derivedAttribute: $derivedAttribute,
                        paramNames: $paramNames,
                        key: $key
                    );
                }
            );
    }

    private function applyDerivedAttribute(object $collection, callable $derivedAttribute, array $paramNames, string $key): object
    {
        $args = [];
        foreach ($paramNames as $param) {
            $args[] = $collection->$param;
        }
        $collection->$key = $derivedAttribute(...$args);

        return $collection;
    }

    private function buildRelationAttribute(string $key, string $relationAttribute): Collection
    {
        return $this->collection->map(
            function (object $item) use ($key, $relationAttribute): mixed {
                return $item->$key = $this->getRelationAttribute(
                    model: $item,
                    relationAttribute: $relationAttribute
                );
            }
        );
    }

    protected function getItemsKeys(): array
    {
        return array_merge(
            $this->attributes,
            array_keys($this->derivedAttributes),
            array_keys($this->relationsAttributes)
        );
    }

    private function getItems(): Collection
    {
        $finalKeys = $this->getItemsKeys();

        return $this->collection->map(function (object $item) use ($finalKeys): mixed {
            return $item->only($finalKeys);
        });
    }
}
