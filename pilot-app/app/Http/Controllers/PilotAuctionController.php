<?php

namespace App\Http\Controllers;

use App\Services\Auction\JsonProviderReadOnlyService;
use Illuminate\Http\JsonResponse;

class PilotAuctionController extends Controller
{
    public function health(): JsonResponse
    {
        return $this->pilotJson([
            'ok' => true,
            'app' => 'pilot-skeleton',
            'route' => '/_pilot/auction/health',
            'ts' => gmdate('c'),
        ]);
    }

    public function getBarang(string $idLelang, JsonProviderReadOnlyService $service): JsonResponse
    {
        $result = $service->getBarang($idLelang);

        return $this->pilotJson($result['payload'], 200, array_merge([
            'X-Pilot-Endpoint' => 'get_barang',
            'X-Pilot-Route-Mode' => 'business-toggle',
        ], $result['headers']));
    }

    public function getPeserta(string $idLelang, JsonProviderReadOnlyService $service): JsonResponse
    {
        $result = $service->getPeserta($idLelang);

        return $this->pilotJson($result['payload'], 200, array_merge([
            'X-Pilot-Endpoint' => 'get_peserta',
            'X-Pilot-Route-Mode' => 'business-toggle',
        ], $result['headers']));
    }

    public function getInitialData(string $idLelang, string $idBarang, JsonProviderReadOnlyService $service): JsonResponse
    {
        return $this->jsonProviderPayloadResponse(
            'get_initial_data',
            'business-toggle',
            $service->getInitialData($idLelang, $idBarang)
        );
    }

    public function getChartUpdate(string $idLelang, JsonProviderReadOnlyService $service): JsonResponse
    {
        return $this->jsonProviderPayloadResponse(
            'get_chart_update',
            'business-toggle',
            $service->getChartUpdate($idLelang)
        );
    }

    public function shadowGetInitialData(string $idLelang, string $idBarang, JsonProviderReadOnlyService $service): JsonResponse
    {
        return $this->jsonProviderPayloadResponse(
            'get_initial_data',
            'shadow-route',
            $service->getInitialData($idLelang, $idBarang)
        );
    }

    public function shadowGetChartUpdate(string $idLelang, JsonProviderReadOnlyService $service): JsonResponse
    {
        return $this->jsonProviderPayloadResponse(
            'get_chart_update',
            'shadow-route',
            $service->getChartUpdate($idLelang)
        );
    }

    public function fallback(?string $path = null): JsonResponse
    {
        return $this->pilotJson(
            [
                'ok' => false,
                'error' => 'NOT_IMPLEMENTED',
                'message' => 'Pilot skeleton route is wired; endpoint contract not implemented yet.',
                'route' => '/_pilot/auction/' . ltrim((string) $path, '/'),
            ],
            501
        );
    }

    private function pilotJson(array $payload, int $status = 200, array $headers = []): JsonResponse
    {
        return response()
            ->json($payload, $status)
            ->header('X-App-Source', 'pilot-skeleton')
            ->withHeaders($headers);
    }

    private function jsonProviderPayloadResponse(string $endpoint, string $routeMode, array $result): JsonResponse
    {
        return $this->pilotJson($result['payload'], 200, array_merge([
            'X-Pilot-Endpoint' => $endpoint,
            'X-Pilot-Route-Mode' => $routeMode,
        ], $result['headers']));
    }
}
