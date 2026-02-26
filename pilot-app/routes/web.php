<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Minimal Wave B pilot stubs to preserve Stage 1/2 coexistence smoke behavior
 * while `pilot-app` is migrated from placeholder to Laravel skeleton.
 */
$pilotJson = static function (array $payload, int $status = 200, array $headers = []): JsonResponse {
    return response()
        ->json($payload, $status)
        ->header('X-App-Source', 'pilot-skeleton')
        ->withHeaders($headers);
};

Route::get('/_pilot/auction/health', function () use ($pilotJson) {
    return $pilotJson([
        'ok' => true,
        'app' => 'pilot-skeleton',
        'route' => '/_pilot/auction/health',
        'ts' => gmdate('c'),
    ]);
});

Route::get('/auction/admin/json_provider/get_barang/{idLelang}', function (string $idLelang) use ($pilotJson) {
    return $pilotJson(
        [
            [
                'id' => 'pilot-item-1',
                'name' => 'Pilot Barang Stub 1',
                'hps' => 100000,
                'hps_in_idr' => '100.000',
                '_pilot' => true,
                '_id_lelang' => $idLelang,
            ],
            [
                'id' => 'pilot-item-2',
                'name' => 'Pilot Barang Stub 2',
                'hps' => 250000,
                'hps_in_idr' => '250.000',
                '_pilot' => true,
                '_id_lelang' => $idLelang,
            ],
        ],
        200,
        [
            'X-Pilot-Endpoint' => 'get_barang',
            'X-Pilot-Route-Mode' => 'business-toggle',
        ]
    );
});

Route::get('/auction/admin/json_provider/get_peserta/{idLelang}', function (string $idLelang) use ($pilotJson) {
    return $pilotJson(
        [
            [
                'id' => 'pilot-peserta-1',
                'name' => 'Pilot Peserta A',
                '_pilot' => true,
                '_id_lelang' => $idLelang,
            ],
            [
                'id' => 'pilot-peserta-2',
                'name' => 'Pilot Peserta B',
                '_pilot' => true,
                '_id_lelang' => $idLelang,
            ],
        ],
        200,
        [
            'X-Pilot-Endpoint' => 'get_peserta',
            'X-Pilot-Route-Mode' => 'business-toggle',
        ]
    );
});

Route::get('/_pilot/auction/{path?}', function (?string $path = null) use ($pilotJson) {
    return $pilotJson(
        [
            'ok' => false,
            'error' => 'NOT_IMPLEMENTED',
            'message' => 'Pilot skeleton route is wired; endpoint contract not implemented yet.',
            'route' => '/_pilot/auction/' . ltrim((string) $path, '/'),
        ],
        501
    );
})->where('path', '.*');
