<?php
require 'vendor/autoload.php';
require 'connect.php';
require 'utils.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
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
} elseif ($method === 'PUT') {
    handlePutRequest();
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}

function handleGetRequest()
{
    $intervention_id = isset($_GET['id']) ? $_GET['id'] : '';

    try {
        $db = Database::getInstance()->getConnection();
        if (empty($intervention_id)) {
            http_response_code(400);
            echo json_encode(["message" => "Intervention ID parameter is required"]);
            exit();
        } else {
            $query = $db->prepare("SELECT * FROM interventions WHERE intervention_id = :id");
            $query->bindParam(':id', $intervention_id);
        }
        $query->execute();

        $intervention = $query->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($intervention);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function handlePostRequest()
{
    $data = json_decode(file_get_contents("php://input"), true);

    $technicien_id = $data['technicien_id'];
    $projet_id = $data['projet_id'];
    $date_intervention = $data['date_intervention'];
    $status = isset($data['status']) ? $data['status'] : 1;
    $obs = isset($data['obs']) ? $data['obs'] : null;

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("INSERT INTO interventions (technicien_id, projet_id, date_intervention, status, obs) VALUES (:technicien_id, :projet_id, TO_DATE(:date_intervention, 'YYYY-MM-DD'), :status, :obs)");
        $query->bindParam(':technicien_id', $technicien_id);
        $query->bindParam(':projet_id', $projet_id);
        $query->bindParam(':date_intervention', $date_intervention);
        $query->bindParam(':status', $status);
        $query->bindParam(':obs', $obs);

        if ($query->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Intervention created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create intervention"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function handlePutRequest()
{
    parse_str(file_get_contents("php://input"), $data);

    $intervention_id = $data['intervention_id'];
    $technicien_id = $data['technicien_id'];
    $projet_id = $data['projet_id'];
    $date_intervention = $data['date_intervention'];
    $status = isset($data['status']) ? $data['status'] : 1;
    $obs = isset($data['obs']) ? $data['obs'] : null;

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("UPDATE interventions SET technicien_id = :technicien_id, projet_id = :projet_id, date_intervention = TO_DATE(:date_intervention, 'YYYY-MM-DD'), status = :status, obs = :obs WHERE intervention_id = :intervention_id");
        $query->bindParam(':technicien_id', $technicien_id);
        $query->bindParam(':projet_id', $projet_id);
        $query->bindParam(':date_intervention', $date_intervention);
        $query->bindParam(':status', $status);
        $query->bindParam(':obs', $obs);
        $query->bindParam(':intervention_id', $intervention_id);

        if ($query->execute()) {
            http_response_code(200);
            echo json_encode(["message" => "Intervention updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update intervention"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
