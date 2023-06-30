<?php

namespace App\Infra\DataSources;

use App\Contracts\DataSource;
use App\Infra\Elasticsearch\ElasticsearchClient;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Support\Collection;

class ComexStatDataSource implements DataSource
{
    /**
     * @var ElasticsearchClient
     */
    protected ElasticsearchClient $elasticsearch;

    /**
     * @param  ElasticsearchClient  $elasticsearchClient
     */
    public function __construct(ElasticsearchClient $elasticsearchClient)
    {
        $this->elasticsearch = $elasticsearchClient;
    }

    /**
     * @param  array  $filters
     * @return Collection
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getNcmChartData(array $filters): Collection
    {
        $aggs = [
            $ncmChart = 'ncm_chart' => [
                'terms' => [
                    'script' => [
                        'source' => "params['_source']['ncm_code'] + ' - ' + params['_source']['ncm_description']"
                    ],
                    'size' => 100
                ]
            ]
        ];

        $queryResults = $this->elasticsearch->search($this->buildParams($filters, $aggs));

        $chartData = collect($queryResults['aggregations'][$ncmChart]['buckets'])->map(function ($item) {
            return [
                'ncm' => $item['key'],
                'operations' => $item['doc_count']
            ];
        });

        return $this->calculatePercentage($chartData);
    }

    /**
     * @param  array  $filters
     * @return Collection
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getStateChartData(array $filters): Collection
    {
        $aggs = [
            $stateChart = 'state_chart' => [
                'terms' => ['field' => 'importer_state', 'size' => 100],
                'aggs' => ['total_fob_value' => ['sum' => ['field' => 'fob_value']]]
            ]
        ];

        $queryResults = $this->elasticsearch->search($this->buildParams($filters, $aggs));

        $chartData = collect($queryResults['aggregations'][$stateChart]['buckets'])->map(function ($item) {
            return [
                'importer_state' => $item['key'],
                'total_fob_value' => $item['total_fob_value']['value'],
                'operations' => $item['doc_count']
            ];
        });

        return $this->calculatePercentage($chartData)->sortByDesc('total_fob_value')->values();
    }

    /**
     * @param  array  $filters
     * @return Collection
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getOriginCountryChartData(array $filters): Collection
    {
        $aggs = [
            $originCountryChart = 'origin_country_chart' => [
                'terms' => ['field' => 'country_origin', 'size' => 100],
                'aggs' => ['total_fob_value' => ['sum' => ['field' => 'fob_value']]]
            ]
        ];

        $queryResults = $this->elasticsearch->search($this->buildParams($filters, $aggs));

        $chartData = collect($queryResults['aggregations'][$originCountryChart]['buckets'])->map(function ($item) {
            return [
                'country_origin' => $item['key'],
                'total_fob_value' => $item['total_fob_value']['value'],
                'operations' => $item['doc_count']
            ];
        });

        return $this->calculatePercentage($chartData, field: 'total_fob_value')
            ->sortByDesc('total_fob_value')
            ->values();
    }

    /**
     * @param  array  $filters
     * @return Collection
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getTransportModeChartData(array $filters): Collection
    {
        $aggs = [
            $transportModeChart = 'transport_mode_chart' => [
                'terms' => ['field' => 'transport_mode', 'size' => 100]
            ]
        ];

        $queryResults = $this->elasticsearch->search($this->buildParams($filters, $aggs));

        $chartData = collect($queryResults['aggregations'][$transportModeChart]['buckets'])->map(function ($item) {
            return [
                'transport_mode' => $item['key'],
                'operations' => $item['doc_count']
            ];
        });

        return $this->calculatePercentage($chartData);
    }

    /**
     * @param  array  $filters
     * @param  array  $aggs
     * @return array
     */
    public function buildParams(array $filters, array $aggs): array
    {
        return [
            'index' => 'comexstat',
            'body' => ['size' => 0, 'query' => $this->buildQuery($filters), 'aggs' => $aggs]
        ];
    }

    /**
     * @param  array  $filters
     * @return array
     */
    protected function buildQuery(array $filters): array
    {
        $query = [];

        foreach ($filters as $filterName => $filter) {
            $filterValues = [];

            $filter = is_array($filter) ? $filter : [];

            foreach ($filter as $value) {
                $filterValues[] = ['prefix' => [$filterName => $value]];
            }

            $query[]['bool']['should'] = $filterValues;
        }

        return ['bool' => ['must' => $query]];
    }

    /**
     * @param  Collection  $chartData
     * @param  string  $field
     * @param  string  $percentageColumnName
     * @return Collection
     */
    protected function calculatePercentage(
        Collection $chartData,
        string $field = 'operations',
        string $percentageColumnName = 'percentage'
    ): Collection {
        $total = $chartData->sum($field);

        return $chartData->map(function ($item) use ($percentageColumnName, $field, $total) {
            $item[$percentageColumnName] = (float)number_format(($item[$field] / $total) * 100, 1);

            return $item;
        });
    }

    /**
     * @param  string  $filter
     * @param  string  $value
     * @return Collection
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getAutocompleteData(string $filter, string $value): Collection
    {
        $params = [
            'index' => 'comexstat',
            'body' => [
                'size' => 0,
                'query' => ['prefix' => [$filter => ['value' => strtolower($value), 'case_insensitive' => true]]],
                'aggs' => ['autocomplete' => ['terms' => ['field' => $filter, 'size' => 10]]]
            ]
        ];

        $queryResults = $this->elasticsearch->search($params);

        return collect($queryResults['aggregations']['autocomplete']['buckets'])->pluck('key');
    }
}
