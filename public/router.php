<?php
$uri = $_SERVER["REQUEST_URI"] ?? '';

if (strpos($uri, '/uploads/') !== false) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Archivo protegido.';
    return true;
}

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico)$/', $uri)) {
    return false;
}

$path = ltrim(parse_url($uri, PHP_URL_PATH), '/');
$_GET['url'] = $path;

require_once __DIR__ . '/index.php';
