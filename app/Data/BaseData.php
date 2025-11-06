<?php

namespace App\Data;

use Spatie\LaravelData\Data;

abstract class BaseData extends Data
{
    public function toFilteredArray(): array
    {
        return array_filter(parent::toArray(), static fn ($value) => ! is_null($value));
    }
}
