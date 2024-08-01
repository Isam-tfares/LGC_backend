<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static $secretKey = 'your_secret_key'; // Replace with your actual secret key

    public static function generate_jwt($payload, $expiration = 3600)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $expiration;
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function decode_jwt($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception('Invalid token: ' . $e->getMessage());
        }
    }
}
