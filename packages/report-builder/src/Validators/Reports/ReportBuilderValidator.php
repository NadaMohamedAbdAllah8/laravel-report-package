<?php

namespace Nada\ReportBuilder\Validators\Reports;

use Illuminate\Database\Eloquent\Model;
use Nada\ReportBuilder\Exceptions\ReportBuilderException;

class ReportBuilderValidator
{
    public static function throwExceptionIfAttributeNotExist(array $attributes, array $items): void
    {
        $columns = array_column($attributes, 0);

        $diff = array_diff($columns, $items);

        if (! empty($diff)) {
            throw new ReportBuilderException('Invalid Sorting Column');
        }
    }

    public static function throwExceptionIfRelationDoesNotExistOnModel(Model $model, string $relation): void
    {
        if (! method_exists($model, $relation)) {
            throw new ReportBuilderException("The relation '{$relation}' does not exist on model ".get_class($model));
        }
    }
}
