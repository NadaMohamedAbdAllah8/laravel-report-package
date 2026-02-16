<?php

use Illuminate\Database\Eloquent\Model;
use Nada\ReportBuilder\Exceptions\ReportBuilderException;
use Nada\ReportBuilder\Validators\Reports\ReportBuilderValidator;

it('throws when an ordered attribute does not exist', function (): void {
    expect(fn (): bool => ReportBuilderValidator::throwExceptionIfAttributeNotExist(
        [['name', 'asc']],
        ['id']
    ))->toThrow(ReportBuilderException::class);
});

it('throws when a relation does not exist on model', function (): void {
    expect(fn (): bool => ReportBuilderValidator::throwExceptionIfRelationDoesNotExistOnModel(
        new class extends Model {},
        'missingRelation'
    ))->toThrow(ReportBuilderException::class);
});
