<?php

namespace App\Validators\Reports;

use App\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Model;

class ReportBuilderValidator
{
    public static function throwExceptionIfAttributeNotExist($attributes, $items)
    {
        $columns = array_column($attributes, 0);

        $diff = array_diff($columns, $items);

        if (!empty($diff)) {
            throw new ValidationException('Invalid Sorting Column');
        }
    }

    public static function throwExceptionIfRelationDoesNotExistOnModel(Model $model, string $relation)
    {
        if (!method_exists($model, $relation)) {
            throw new ValidationException("The relation '{$relation}' does not exist on model " . get_class($model));
        }
    }
}

