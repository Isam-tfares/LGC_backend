<?php
require 'vendor/autoload.php';
require 'connect.php';
require 'utils.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();

    $query = $db->prepare("SELECT * FROM users WHERE user_type='technicien'");
    $query->execute();
    $techniciens = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($techniciens);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
