<?php

namespace Nada\ReportBuilder\Reports\Criteria\Order;

use Illuminate\Database\Eloquent\Builder;
use Nada\ReportBuilder\Reports\BaseReportBuilder;
use Nada\ReportBuilder\Validators\Reports\ReportBuilderValidator;

class OrderByRelatedColumn implements OrderCriteria
{
    public function __construct(
        protected string $relation,
        protected string $column,
        protected string $direction = BaseReportBuilder::SORT_ASC
    ) {}

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
