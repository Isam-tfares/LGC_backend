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

// Get the date from the query parameters
$date_reception = isset($_GET['date_reception']) ? $_GET['date_reception'] : '';

if (empty($date_reception)) {
    http_response_code(400);
    echo json_encode(["message" => "Date parameter is required"]);
    exit();
}
$date_reception = date('d/m/Y', strtotime($date_reception));
// echo $date_reception;


try {
    $db = Database::getInstance()->getConnection();
    $query = $db->prepare("select *
from receptions
where Date_reception = to_date(:date_reception, 'DD/MM/YYYY')
");
    $query->bindParam(':date_reception', $date_reception);
    $query->execute();

    $receptions = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($receptions);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
