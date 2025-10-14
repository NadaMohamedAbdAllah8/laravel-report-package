<?php
namespace App\Reports\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface Criteria
{
    public function apply(Builder $query): Builder;
}