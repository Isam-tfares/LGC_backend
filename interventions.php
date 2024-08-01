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

// Get the user ID and date from the query parameters
$user_id = isset($_GET['user']) ? $_GET['user'] : '';
$date_intervention = isset($_GET['date_intervention']) ? $_GET['date_intervention'] : '';

try {
    $db = Database::getInstance()->getConnection();
    if (empty($user_id) && empty($date_intervention)) {
        $query = $db->prepare("SELECT * FROM interventions");
    } elseif (empty($user_id) && !empty($date_intervention)) {
        $date_intervention = date('d/m/Y', strtotime($date_intervention));
        $query = $db->prepare("SELECT * FROM interventions WHERE date_intervention = TO_DATE(:date_intervention, 'DD/MM/YYYY')");
        $query->bindParam(':date_intervention', $date_intervention);
    } elseif (!empty($user_id) && empty($date_intervention)) {
        $query = $db->prepare("SELECT * FROM interventions WHERE technicien_id = :user");
        $query->bindParam(':user', $user_id);
    } else {
        $date_intervention = date('d/m/Y', strtotime($date_intervention));
        $query = $db->prepare("SELECT * FROM interventions WHERE technicien_id = :user AND date_intervention = TO_DATE(:date_intervention, 'DD/MM/YYYY')");
        $query->bindParam(':user', $user_id);
        $query->bindParam(':date_intervention', $date_intervention);
    }
    $query->execute();

    $interventions = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($interventions);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
