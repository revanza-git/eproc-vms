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

http_response_code(404);
echo json_encode([
    'ok' => false,
    'error' => 'NOT_FOUND',
    'route' => $uri,
]);
