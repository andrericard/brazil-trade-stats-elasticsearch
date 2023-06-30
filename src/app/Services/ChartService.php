<?php

namespace App\Services;

use App\Contracts\DataSource;
use App\Enums\ChartEnum;
use Illuminate\Support\Collection;

class ChartService
{
    /**
     * @var DataSource
     */
    protected DataSource $dataSource;

    /**
     * @param  DataSource  $dataSource
     */
    public function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @param  string  $chartName
     * @param  array  $filters
     * @return Collection
     */
    public function getDataForNamedChart(string $chartName, array $filters): Collection
    {
        return match ($chartName) {
            ChartEnum::NCM->value => $this->getNcmChartData($filters),
            ChartEnum::STATE->value => $this->getStateChartData($filters),
            ChartEnum::ORIGIN_COUNTRY->value => $this->getOriginCountryChartData($filters),
            ChartEnum::TRANSPORT_MODE->value => $this->getTransportModeChartData($filters),
        };
    }

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getNcmChartData(array $filters): Collection
    {
        return $this->dataSource->getNcmChartData($filters);
    }

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getStateChartData(array $filters): Collection
    {
        return $this->dataSource->getStateChartData($filters);
    }

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getTransportModeChartData(array $filters): Collection
    {
        return $this->dataSource->getTransportModeChartData($filters);
    }

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getOriginCountryChartData(array $filters): Collection
    {
        return $this->dataSource->getOriginCountryChartData($filters);
    }

    /**
     * @param  string  $filter
     * @param  string  $value
     * @return Collection
     */
    public function getAutocompleteData(string $filter, string $value): Collection
    {
        return $this->dataSource->getAutocompleteData($filter, $value);
    }
}
