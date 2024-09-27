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

        $matricule = $data->matricule ?? '';
        $password = $data->password ?? '';

        if (empty($matricule) || empty($password)) {
            http_response_code(400);
            echo json_encode(["error" => "Matricule and password are required.", "error" => "invalid data"]);
            return;
        }
        $user = User::userConn($matricule);
        if (!isset($user['motpasse'])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid matricule or password."]);
            return;
        }
        if ($user && $password == $user['motpasse']) {
            $token = [
                "iss" => "your_domain.com",
                "aud" => "your_domain.com",
                "iat" => time(),
                "exp" => time() + 7200, // 2hours
                "data" => [
                    "id" => $user['IDPersonnel'],
                    "username" => $user['mle_personnel'],
                    "user_type" => $user['IDFonction_personnel'],
                    "user_tache" => $user['IDTache_personnel'],
                    "IDAgence" => $user['IDAgence'],
                ]
            ];

            $jwt = JWT::encode($token, $secret_key, $jwt_algorithm);

            http_response_code(200);
            echo json_encode([
                "message" => "Login successful.",
                "jwt" => $jwt,
                "user" => [
                    "id" => $user['IDPersonnel'],
                    "username" => $user['mle_personnel'],
                    "fullname" => $user['Nom_personnel'],
                    "user_type" => $user['IDFonction_personnel'],
                    "user_tache" => $user['IDTache_personnel'],
                    "IDAgence" => $user['IDAgence'],
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Invalid matricule or password."]);
        }
    }
}
