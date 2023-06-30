<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface DataSource
{
    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getNcmChartData(array $filters): Collection;

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getStateChartData(array $filters): Collection;

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getOriginCountryChartData(array $filters): Collection;

    /**
     * @param  array  $filters
     * @return Collection
     */
    public function getTransportModeChartData(array $filters): Collection;

    /**
     * @param  string  $filter
     * @param  string  $value
     * @return Collection
     */
    public function getAutocompleteData(string $filter, string $value): Collection;
}
