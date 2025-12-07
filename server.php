<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_contains($uri, '..')) {
    http_response_code(400);
    echo "400 Bad Request";
    exit;
}

$real = realpath(__DIR__ . $uri);

$allowedDirs = [
    realpath(__DIR__ . '/storage'),
];

if ($real && in_array(dirname($real), $allowedDirs) && is_file($real)) {
    return false;
}

require __DIR__ . '/index.php';
