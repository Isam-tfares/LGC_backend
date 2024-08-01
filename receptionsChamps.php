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

    $query = $db->prepare("SELECT * FROM betontypes");
    $query->execute();
    $betontypes = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->prepare("SELECT * FROM echantillonnatures");
    $query->execute();
    $echantillonnatures = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->prepare("SELECT * FROM essaie_type");
    $query->execute();
    $essaie_type = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->prepare("SELECT * FROM materiaux");
    $query->execute();
    $materiaux = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->prepare("SELECT * FROM prestations");
    $query->execute();
    $prestations = $query->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "betontypes" => $betontypes,
        "echantillonnatures" => $echantillonnatures,
        "essaie_type" => $essaie_type,
        "materiaux" => $materiaux,
        "prestations" => $prestations
    ];
    http_response_code(200);
    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
