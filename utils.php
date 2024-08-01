<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
// Ensure the user is authenticated by checking the JWT token
function isAuthenticated()
{
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return false;
    }

    $jwt = $matches[1];

    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $secret_key = $_ENV['JWT_SECRET'];
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        return true;
    } catch (Exception $e) {
        return false;
    }
}
