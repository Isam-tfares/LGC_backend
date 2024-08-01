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

    $client_id = isset($_GET['client_id']) ? $_GET['client_id'] : '';
    if (empty($client_id)) {
        $query = $db->prepare("SELECT * FROM projets ");
    } else {
        $query = $db->prepare("SELECT * FROM projets WHERE client_id = :id");
        $query->bindParam(':id', $client_id);
    }

    $query->execute();
    $projects = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($projects);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
