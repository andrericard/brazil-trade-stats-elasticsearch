<?php

namespace Tests\App\Http\Controllers\Api;

use App\Http\Controllers\Api\ChartController;
use App\Http\Requests\Api\GetChartDataRequest;
use Mockery;
use Tests\TestCase;
use App\Services\ChartService;

class ChartControllerTest extends TestCase
{
    public function testGetChartData()
    {
        $chartService = Mockery::spy(ChartService::class);

        $chartController = new ChartController($chartService);

        $getChartDataRequest = new GetChartDataRequest([
            'filters' => ['test']
        ]);

        $chartName = 'ncm';

        $chartController->getChartData($getChartDataRequest, $chartName);

        $this->assertTrue(true);
    }
}
