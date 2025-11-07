<?php

namespace App\Reports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaginatedReportBuilder extends BaseReportBuilder
{
    const CACHE_PREFIX = 'count_';
    const CACHE_TIME_IN_MINUTES = 30;

    const DEFAULT_PER_PAGE = 15;
    const DEFAULT_PAGE = 1;

    private int $perPage;
    private int $page = self::DEFAULT_PAGE;
    private array $pagination = [];

    /**
     * Set the number of items per page for pagination.
     */
    public function paginate(int $perPage, ?int $page = self::DEFAULT_PAGE): self
    {
        $this->perPage = $perPage;
        $this->page = $page;

        return $this;
    }

    /**
     * Get items and expression values with pagination metadata.
     */
    public function get(): Collection
    {
        Log::info('[ReportBuilder@get] Retrieving report data');

        $collection = parent::get();

        $collection['pagination'] = $this->pagination;

        return $collection;
    }


    /**
     * Applies pagination to the query and stores pagination metadata.
     */
    private function buildPaginatedAttributes(): BaseReportBuilder
    {
        $totalSizeQuery = clone $this->query;

        $sqlQuery = $totalSizeQuery->toSql();
        $queryBindings = $totalSizeQuery->getBindings();
        $cacheKey = self::CACHE_PREFIX . md5($sqlQuery . json_encode($queryBindings));

        $totalSize = Cache::remember(
            $cacheKey,
            now()->addMinutes(self::CACHE_TIME_IN_MINUTES),
            function () use ($totalSizeQuery): int {
            return $totalSizeQuery->count('id');
        });

        $totalPages = (int) ceil(($totalSize ?: 0) / max($this->perPage, 1));

        $paginator = $this->query->simplePaginate(
            perPage: $this->perPage,
            page: $this->page
        );

        Log::info('[ReportBuilder] Query after applying pagination', [
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
        $this->buildPaginatedAttributes();
        $this->buildRelationAttributes();
        $this->buildDerivedAttributes();

        return $this;
    }
}
