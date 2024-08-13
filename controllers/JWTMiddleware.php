<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTMiddleware
{
    public static function validateToken()
    {
        $headers = apache_request_headers();
        if (!isset($headers['authorization'])) {
            http_response_code(401);
            return ["message" => "Token is required. 1"];
        }

        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            http_response_code(401);
            return ["message" => "Token is required. 2"];
        }

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return (array) $decoded->data; // Convert stdClass object to array
        } catch (Exception $e) {
            http_response_code(401);
            return ["message" => "Access denied.", "error" => $e->getMessage()];
        }
    }
}
