<?php
require 'vendor/autoload.php';
require 'connect.php';

use \Firebase\JWT\JWT;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$secret_key = $_ENV['JWT_SECRET'];
$jwt_algorithm = 'HS256';

// Get the posted data
$data = json_decode(file_get_contents("php://input"));

$username = $data->username ?? '';
$password = $data->password ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["message" => "Username and password are required."]);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    $query = $db->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['PASSWORD']) {
        $token = [
            "iss" => "your_domain.com",
            "aud" => "your_domain.com",
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => [
                "id" => $user['user_id'],
                "username" => $user['username']
            ]
        ];

        $jwt = JWT::encode($token, $secret_key, $jwt_algorithm);

        http_response_code(200);
        echo json_encode([
            "message" => "Login successful.",
            "jwt" => $jwt,
            "user" => [
                "id" => $user['user_id'],
                "username" => $user['username'],
                "fullname" => $user['nom_complet']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid username or password."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
