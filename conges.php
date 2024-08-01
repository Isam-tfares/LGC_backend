<?php
require 'vendor/autoload.php';
require 'connect.php';
require 'utils.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    handleGetRequest();
} elseif ($method === 'POST') {
    handlePostRequest();
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}

function handleGetRequest()
{
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';

    if (empty($user_id) || empty($year)) {
        http_response_code(400);
        echo json_encode(["message" => "User ID and year parameters are required"]);
        return;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("SELECT SUM(jours_pris) as total_days FROM conges WHERE user_id = :user_id AND annee = :year");
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':year', $year);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($result);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function handlePostRequest()
{
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id = $data['user_id'];
    $annee = $data['annee'];
    $start_date = $data['start_date'];
    $jours_pris = $data['jours_pris'];

    if (empty($user_id) || empty($annee) || empty($start_date) || empty($jours_pris)) {
        http_response_code(400);
        echo json_encode(["message" => "All fields (user_id, annee, start_date, jours_pris) are required"]);
        return;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("INSERT INTO conges (user_id, annee, start_date, jours_pris) VALUES (:user_id, :annee, TO_DATE(:start_date, 'YYYYMMDD'), :jours_pris)");
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':annee', $annee);
        $query->bindParam(':start_date', $start_date);
        $query->bindParam(':jours_pris', $jours_pris);

        if ($query->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Leave record created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create leave record"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
