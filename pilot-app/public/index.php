<?php

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

header('Content-Type: application/json');
header('X-App-Source: pilot-skeleton');

if ($method === 'GET' && $uri === '/_pilot/auction/health') {
    http_response_code(200);
    echo json_encode([
        'ok' => true,
        'app' => 'pilot-skeleton',
        'route' => $uri,
        'ts' => gmdate('c'),
    ]);
    exit;
}

if ($method === 'GET' && preg_match('#^/_pilot/auction/#', $uri) === 1) {
    http_response_code(501);
    echo json_encode([
        'ok' => false,
        'error' => 'NOT_IMPLEMENTED',
        'message' => 'Pilot skeleton route is wired; endpoint contract not implemented yet.',
        'route' => $uri,
    ]);
    exit;
}

if (
    $method === 'GET'
    && preg_match('#^/auction/admin/json_provider/(get_barang|get_peserta)/([^/]+)$#', $uri, $matches) === 1
) {
    $endpoint = $matches[1];
    $idLelang = $matches[2];
    header('X-Pilot-Endpoint: ' . $endpoint);
    header('X-Pilot-Route-Mode: business-toggle');

    http_response_code(200);
    if ($endpoint === 'get_barang') {
        echo json_encode([
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
        ]);
        exit;
    }

    echo json_encode([
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
    ]);
    exit;
}

http_response_code(404);
echo json_encode([
    'ok' => false,
    'error' => 'NOT_FOUND',
    'route' => $uri,
]);
