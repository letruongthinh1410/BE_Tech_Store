<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'success' => true,
    'message' => 'API is working!',
    'server' => $_SERVER['SERVER_SOFTWARE'],
    'php_version' => PHP_VERSION,
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => __FILE__
]);
?>