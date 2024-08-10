<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class LoginController
{
    static public function login()
    {
        $secret_key = $_ENV['JWT_SECRET'];
        $jwt_algorithm = 'HS256';
        $data = json_decode(file_get_contents("php://input"));

        $username = $data->username ?? '';
        $password = $data->password ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            return json_encode(["message" => "Username and password are required.", "error" => "invalid data"]);
        }
        $user = User::userConn($username);
        if ($user && $password == $user['PASSWORD']) {
            $token = [
                "iss" => "your_domain.com",
                "aud" => "your_domain.com",
                "iat" => time(),
                "exp" => time() + 3600,
                "data" => [
                    "id" => $user['user_id'],
                    "username" => $user['username'],
                    "user_type" => $user['user_type']
                ]
            ];

            $jwt = JWT::encode($token, $secret_key, $jwt_algorithm);

            http_response_code(200);
            return json_encode([
                "message" => "Login successful.",
                "jwt" => $jwt,
                "user" => [
                    "id" => $user['user_id'],
                    "username" => $user['username'],
                    "fullname" => $user['nom_complet'],
                    "user_type" => $user['user_type'],
                ]
            ]);
        } else {
            http_response_code(401);
            return json_encode(["message" => "Invalid username or password."]);
        }
    }
}
