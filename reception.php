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
    $reception_id = isset($_GET['id']) ? $_GET['id'] : '';

    try {
        $db = Database::getInstance()->getConnection();
        if (empty($reception_id)) {
            http_response_code(400);
            echo json_encode(["message" => "Reception ID parameter is required"]);
            exit();
        } else {
            $query = $db->prepare("SELECT * FROM receptions WHERE reception_id = :id");
            $query->bindParam(':id', $reception_id);
        }
        $query->execute();

        $receptions = $query->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($receptions);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function handlePostRequest()
{
    $data = json_decode(file_get_contents("php://input"), true);

    $intervention_id = $data['intervention_id'];
    $code_bar = $data['code_bar'];
    $client_id = $data['client_id'];
    $projet_id = $data['projet_id'];
    $prestation_id = $data['prestation_id'];
    $materiaux_id = $data['materiaux_id'];
    $nbr_echantillon = $data['nbr_echantillon'];
    $N_reception = $data['N_reception'];
    $Date_reception = $data['Date_reception'];
    $Date_confuction = $data['Date_confuction'];
    $Etat_recuperation = $data['Etat_recuperation'];
    $Preleve_par = $data['Preleve_par'];
    $technicien_id = $data['technicien_id'];
    $type_reception = $data['type_reception'];
    $essaie = $data['essaie'];
    $beton_type = $data['beton_type'];
    $slump = $data['slump'];
    $copmression = $data['copmression'];
    $pendage = $data['pendage'];
    $flexion = $data['flexion'];
    $lieu_de_prelevemnt = $data['lieu_de_prelevemnt'];
    $nature_echantillon_id = $data['nature_echantillon_id'];
    $obs = isset($data['obs']) ? $data['obs'] : null;
    $mode_confuction = $data['mode_confuction'];
    $central_beton = $data['central_beton'];
    $BL = $data['BL'];
    $nbr_jrs = $data['nbr_jrs'];

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("INSERT INTO receptions (
            intervention_id, code_bar, client_id, projet_id, prestation_id, materiaux_id, nbr_echantillon,
            N_reception, Date_reception, Date_confuction, Etat_recuperation, Preleve_par, technicien_id,
            type_reception, essaie, beton_type, slump, copmression, pendage, flexion, lieu_de_prelevemnt,
            nature_echantillon_id, obs, mode_confuction, central_beton, BL, nbr_jrs
        ) VALUES (
            :intervention_id, :code_bar, :client_id, :projet_id, :prestation_id, :materiaux_id, :nbr_echantillon,
            :N_reception, TO_DATE(:Date_reception, 'YYYYMMDD'), TO_DATE(:Date_confuction, 'YYYYMMDD'), :Etat_recuperation, 
            :Preleve_par, :technicien_id, :type_reception, :essaie, :beton_type, :slump, :copmression, :pendage, :flexion,
            :lieu_de_prelevemnt, :nature_echantillon_id, :obs, :mode_confuction, :central_beton, :BL, :nbr_jrs
        )");
        $query->bindParam(':intervention_id', $intervention_id);
        $query->bindParam(':code_bar', $code_bar);
        $query->bindParam(':client_id', $client_id);
        $query->bindParam(':projet_id', $projet_id);
        $query->bindParam(':prestation_id', $prestation_id);
        $query->bindParam(':materiaux_id', $materiaux_id);
        $query->bindParam(':nbr_echantillon', $nbr_echantillon);
        $query->bindParam(':N_reception', $N_reception);
        $query->bindParam(':Date_reception', $Date_reception);
        $query->bindParam(':Date_confuction', $Date_confuction);
        $query->bindParam(':Etat_recuperation', $Etat_recuperation);
        $query->bindParam(':Preleve_par', $Preleve_par);
        $query->bindParam(':technicien_id', $technicien_id);
        $query->bindParam(':type_reception', $type_reception);
        $query->bindParam(':essaie', $essaie);
        $query->bindParam(':beton_type', $beton_type);
        $query->bindParam(':slump', $slump);
        $query->bindParam(':copmression', $copmression);
        $query->bindParam(':pendage', $pendage);
        $query->bindParam(':flexion', $flexion);
        $query->bindParam(':lieu_de_prelevemnt', $lieu_de_prelevemnt);
        $query->bindParam(':nature_echantillon_id', $nature_echantillon_id);
        $query->bindParam(':obs', $obs);
        $query->bindParam(':mode_confuction', $mode_confuction);
        $query->bindParam(':central_beton', $central_beton);
        $query->bindParam(':BL', $BL);
        $query->bindParam(':nbr_jrs', $nbr_jrs);

        if ($query->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Reception created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create reception"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function handlePutRequest()
{
    parse_str(file_get_contents("php://input"), $data);

    $reception_id = $data['reception_id'];
    $intervention_id = $data['intervention_id'];
    $code_bar = $data['code_bar'];
    $client_id = $data['client_id'];
    $projet_id = $data['projet_id'];
    $prestation_id = $data['prestation_id'];
    $materiaux_id = $data['materiaux_id'];
    $nbr_echantillon = $data['nbr_echantillon'];
    $N_reception = $data['N_reception'];
    $Date_reception = $data['Date_reception'];
    $Date_confuction = $data['Date_confuction'];
    $Etat_recuperation = $data['Etat_recuperation'];
    $Preleve_par = $data['Preleve_par'];
    $technicien_id = $data['technicien_id'];
    $type_reception = $data['type_reception'];
    $essaie = $data['essaie'];
    $beton_type = $data['beton_type'];
    $slump = $data['slump'];
    $copmression = $data['copmression'];
    $pendage = $data['pendage'];
    $flexion = $data['flexion'];
    $lieu_de_prelevemnt = $data['lieu_de_prelevemnt'];
    $nature_echantillon_id = $data['nature_echantillon_id'];
    $obs = isset($data['obs']) ? $data['obs'] : null;
    $mode_confuction = $data['mode_confuction'];
    $central_beton = $data['central_beton'];
    $BL = $data['BL'];
    $nbr_jrs = $data['nbr_jrs'];

    try {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("UPDATE receptions SET
            intervention_id = :intervention_id, code_bar = :code_bar, client_id = :client_id, projet_id = :projet_id,
            prestation_id = :prestation_id, materiaux_id = :materiaux_id, nbr_echantillon = :nbr_echantillon,
            N_reception = :N_reception, Date_reception = TO_DATE(:Date_reception, 'YYYYMMDD'), Date_confuction = TO_DATE(:Date_confuction, 'YYYYMMDD'),
            Etat_recuperation = :Etat_recuperation, Preleve_par = :Preleve_par, technicien_id = :technicien_id, type_reception = :type_reception,
            essaie = :essaie, beton_type = :beton_type, slump = :slump, copmression = :copmression, pendage = :pendage,
            flexion = :flexion, lieu_de_prelevemnt = :lieu_de_prelevemnt, nature_echantillon_id = :nature_echantillon_id, 
            obs = :obs, mode_confuction = :mode_confuction, central_beton = :central_beton, BL = :BL, nbr_jrs = :nbr_jrs
            WHERE reception_id = :reception_id");
        $query->bindParam(':reception_id', $reception_id);
        $query->bindParam(':intervention_id', $intervention_id);
        $query->bindParam(':code_bar', $code_bar);
        $query->bindParam(':client_id', $client_id);
        $query->bindParam(':projet_id', $projet_id);
        $query->bindParam(':prestation_id', $prestation_id);
        $query->bindParam(':materiaux_id', $materiaux_id);
        $query->bindParam(':nbr_echantillon', $nbr_echantillon);
        $query->bindParam(':N_reception', $N_reception);
        $query->bindParam(':Date_reception', $Date_reception);
        $query->bindParam(':Date_confuction', $Date_confuction);
        $query->bindParam(':Etat_recuperation', $Etat_recuperation);
        $query->bindParam(':Preleve_par', $Preleve_par);
        $query->bindParam(':technicien_id', $technicien_id);
        $query->bindParam(':type_reception', $type_reception);
        $query->bindParam(':essaie', $essaie);
        $query->bindParam(':beton_type', $beton_type);
        $query->bindParam(':slump', $slump);
        $query->bindParam(':copmression', $copmression);
        $query->bindParam(':pendage', $pendage);
        $query->bindParam(':flexion', $flexion);
        $query->bindParam(':lieu_de_prelevemnt', $lieu_de_prelevemnt);
        $query->bindParam(':nature_echantillon_id', $nature_echantillon_id);
        $query->bindParam(':obs', $obs);
        $query->bindParam(':mode_confuction', $mode_confuction);
        $query->bindParam(':central_beton', $central_beton);
        $query->bindParam(':BL', $BL);
        $query->bindParam(':nbr_jrs', $nbr_jrs);

        if ($query->execute()) {
            http_response_code(200);
            echo json_encode(["message" => "Reception updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update reception"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
