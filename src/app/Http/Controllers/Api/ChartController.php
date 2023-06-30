<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    /**
     * @var ChartService
     */
    protected ChartService $chartService;

    /**
     * @param  ChartService  $chartService
     */
    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    /**
     * @param  Request  $request
     * @param  string  $chartName
     * @return JsonResponse
     */
    public function getChartData(Request $request, string $chartName): JsonResponse
    {
        $request->validate([
            'filters' => 'array|required',
        ]);

        $chartData = $this->chartService->getDataForNamedChart($chartName, $request['filters']);

        return response()->json(['data' => $chartData]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getAutocompleteData(Request $request): JsonResponse
    {
        $request->validate([
            'filter' => 'required|in:ncm_code,importer_state,transport_mode,country_origin',
            'value' => 'required',
        ]);

        $autocompleteData = $this->chartService->getAutocompleteData($request['filter'], $request['value']);

        return response()->json(['data' => $autocompleteData]);
    }
}
