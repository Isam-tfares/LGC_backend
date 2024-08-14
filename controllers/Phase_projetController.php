<?php
class Phase_projetController
{
    public static function ReceptionsChef()
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        if ($fromDate == '' || $toDate == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid date range"]);
            return;
        }
        $receptions = Phase_projet::getAllReceptions($fromDate, $toDate);
        return $receptions;
    }
    public static function PreReceptionsChef()
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        if ($fromDate == '' || $toDate == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid date range"]);
            return;
        }
        $preReceptions = Phase_projet::getAllPreReceptions($fromDate, $toDate);
        return $preReceptions;
    }
    public static function getReception()
    {
        $data = json_decode(file_get_contents("php://input"));
        $reception_id = $data->reception_id ?? '';
        if ($reception_id == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid reception ID"]);
            return;
        }
        $reception = Phase_projet::getReception($reception_id);
        return $reception;
    }
    public static function getPreReception()
    {
        $data = json_decode(file_get_contents("php://input"));
        $reception_id = $data->IDPre_reception ?? '';
        if ($reception_id == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid reception ID"]);
            return;
        }
        $reception = Phase_projet::getPreReception($reception_id);
        return $reception;
    }
    public static function validatePrereception($user_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $reception_id = $data->reception_id ?? '';
        // extract here other data
        if ($reception_id == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid reception ID"]);
            return;
        }
        $response = Phase_projet::validateReception($user_id, $reception_id);
        return $response;
    }
    public static function newReceptionTec($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            !isset($data['IDPhase']) || !isset($data['IDProjet']) || !isset($data['nombre']) || !isset($data['IDType_beton'])
            || !isset($data['IDMateriaux']) || !isset($data['observation']) || !isset($data['date_debut']) ||
            !isset($data['date_fin']) || !isset($data['date_prevus']) || !isset($data['prelevement_par'])
            || !isset($data['Compression']) || !isset($data['Traction']) || !isset($data['Lieux_ouvrage'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Please provide all the required data"]);
            return;
        }
        $response = Phase_projet::insertPreReception($data, $user_id);
        return $response;
    }
}
