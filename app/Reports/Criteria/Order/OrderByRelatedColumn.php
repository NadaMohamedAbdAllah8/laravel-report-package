<?php

namespace App\Reports\Criteria\Order;

use App\Enums\SortingType;
use App\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Builder;

use App\Reports\Criteria\Criteria;
use App\Validators\Reports\ReportBuilderValidator;

class OrderByRelatedColumn implements Criteria
{
    protected string $relation;
    protected string $column;
    protected string $direction;

    public function __construct(string $relation, string $column, string $direction = SortingType::ASC->value)
    {
        $this->relation = $relation;
        $this->column = $column;
        $this->direction = $direction;
    }

    public function apply(Builder $query): Builder
    {
        $model = $query->getModel();
        ReportBuilderValidator::throwExceptionIfRelationDoesNotExistOnModel(model: $model, relation: $this->relation);
        $relatedModelRelation = $query->getRelation($this->relation);

        $relatedModel = $relatedModelRelation->getRelated();

        $localTable = $model->getTable();
        $relatedTable = $relatedModel->getTable();

        $foreignKey = $relatedModelRelation->getForeignKeyName();
        $ownerKey = $relatedModel->getKeyName();

        $query->select("$localTable.*");

        $query->leftJoin(
            $relatedTable,
            "$localTable.$foreignKey",
            '=',
            "$relatedTable.$ownerKey"
        )
            ->orderBy("$relatedTable.$this->column", $this->direction);

        return $query;
    }
}
