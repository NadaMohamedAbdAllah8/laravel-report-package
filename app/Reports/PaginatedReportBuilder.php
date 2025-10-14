<?php

namespace App\Reports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaginatedReportBuilder extends BaseReportBuilder
{
    private ?int $paginate = null;
    private int $page = 1;
    private array $pagination = [];

    /**
     * Set the number of items per page for pagination.
     */
    public function paginate(?int $perPage = null, ?int $page = 1): self
    {
        Log::info('[PaginatedReportBuilder] Setting pagination parameters', [
            'per_page' => $perPage,
            'page' => $page,
        ]);

        $this->paginate = $perPage;
        $this->page = $page ?? 1;

        return $this;
    }

    /**
     * Get items and expression values with pagination metadata.
     */
    public function get(): Collection
    {
        Log::info('[PaginatedReportBuilder@get] Retrieving report data');

        $collection = parent::get();

        if (!is_null($this->paginate)) {
            Log::debug('[PaginatedReportBuilder@get] Adding pagination metadata', [
                'pagination' => $this->pagination,
            ]);
            $collection['pagination'] = $this->pagination;
        }

        return $collection;
    }

    /**
     * Builds the report attributes.
     * If pagination is enabled, applies pagination before building attributes.
     */
    protected function buildAttributes(): BaseReportBuilder
    {
        if (is_null($this->paginate)) {
            Log::debug('[PaginatedReportBuilder] Pagination is disabled, using parent attributes');
            return parent::buildAttributes();
        }

        return $this->applyPagination();
    }

    /**
     * Applies pagination to the query and stores pagination metadata.
     */
    private function applyPagination(): BaseReportBuilder
    {
        $totalSizeQuery = clone $this->query;

        $sqlQuery = $totalSizeQuery->toSql();
        $queryBindings = $totalSizeQuery->getBindings();
        $cacheKey = 'count_' . md5($sqlQuery . json_encode($queryBindings));

        if (Cache::has($cacheKey)) {
            Log::debug('[PaginatedReportBuilder] Count key is cached', [
                'cached_key' => $cacheKey,
            ]);
        } else {
            Log::debug('[PaginatedReportBuilder] Count key is not cached', [
                'cached_key' => $cacheKey,
            ]);
        }

        $totalSize = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($totalSizeQuery) {
            return $totalSizeQuery->count('id');
        });

        $perPage = $this->paginate ?? 15;
        $totalPages = (int) ceil(($totalSize ?: 0) / max($perPage, 1));

        $paginator = $this->query->simplePaginate(
            perPage: $perPage,
            page: $this->page
        );

        Log::info('[PaginatedReportBuilder] Query after applying pagination', [
            'sql' => $this->query->toSql(),
            'queryBindings' => $this->query->getBindings(),
        ]);

        $this->pagination = [
            'size' => $paginator->count(),
            'page' => $paginator->currentPage(),
            'total_pages' => $totalPages,
            'total_size' => $totalSize,
            'per_page' => $paginator->perPage(),
        ];

        $this->collection = collect($paginator->items());

        return $this;
    }

    /**
     * Builds the full report with attributes and criteria when available.
     */
    public function build(): BaseReportBuilder
    {
        $this->applyCriteria();
        $this->buildAttributes();
        $this->buildRelationAttributes();
        $this->buildDerivedAttributes();

        return $this;
    }
}
