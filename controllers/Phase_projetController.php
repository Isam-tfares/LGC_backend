<?php
class Phase_projetController
{
    public static function ReceptionsChef($IDAgence)
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        if ($fromDate == '' || $toDate == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid date range"]);
            return;
        }
        $receptions = Phase_projet::getAllReceptions($fromDate, $toDate, $IDAgence);
        return $receptions;
    }
    public static function PreReceptionsChef($IDAgence)
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        if ($fromDate == '' || $toDate == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid date range"]);
            return;
        }
        $preReceptions = Phase_projet::getAllPreReceptions($fromDate, $toDate, $IDAgence);
        return $preReceptions;
    }
    public static function getReceptionByIntervention()
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        if ($intervention_id == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid reception ID"]);
            return;
        }
        $reception = Phase_projet::getReceptionByIntervention($intervention_id);
        return $reception;
    }
    public static function getPreReceptionByIntervention()
    {
        $data = json_decode(file_get_contents("php://input"));
        $reception_id = $data->intervention_id ?? '';
        if ($reception_id == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid reception ID"]);
            return;
        }
        $reception = Phase_projet::getPreReceptionByIntervention($reception_id);
        return $reception;
    }
    public static function validatePrereception($user_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $IDPre_reception = $data->IDPre_reception ?? '';
        // extract here other data
        if ($IDPre_reception == '') {
            http_response_code(400);
            echo json_encode(["message" => "Please provide a valid IDPre_reception"]);
            return;
        }
        $response = Phase_projet::validateReception($user_id, $IDPre_reception);
        return $response;
    }
    public static function UpdatePreReception($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // if (
        //     // !isset($data['IDPre_reception']) || 
        //     !isset($data['IDPhase']) || !isset($data['IDProjet'])
        //     || !isset($data['nombre']) || !isset($data['IDType_beton']) || !isset($data['IDMateriaux'])
        //     || !isset($data['observation']) || !isset($data['date_prevus']) || !isset($data['prelevement_par'])
        //     || !isset($data['Compression']) || !isset($data['Traction']) || !isset($data['Lieux_ouvrage'])
        //     || !isset($data['Traction_fend'])
        //     // || !isset($data['IDPersonnel'])
        // ) {
        //     http_response_code(400);
        //     echo json_encode(["message" => "Please provide all required fields"]);
        //     return;
        // }
        $requiredFields = [
            'IDPhase',
            'IDProjet',
            'nombre',
            'IDType_beton',
            'IDMateriaux',
            'observation',
            // 'date_prevus',
            'prelevement_par',
            'Compression',
            'Traction',
            'Lieux_ouvrage',
            'Traction_fend'
        ];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            http_response_code(400);
            echo json_encode([
                "message" => "Please provide all required fields",
                "missing_fields" => $missingFields
            ]);
            return;
        }


        $response = Phase_projet::updateReception($user_id, $data);
        return $response;
    }
    // intervention_id,IDPhase, IDProjet, nombre, IDType_beton, IDMateriaux, observation, date_prevus, prelevement_par, Compression, Traction, Lieux_ouvrage, Traction_fend
    public static function newReceptionTec($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            !isset($data['intervention_id']) || !isset($data['IDPhase']) || !isset($data['IDProjet'])
            || !isset($data['nombre']) || !isset($data['IDType_beton']) || !isset($data['IDMateriaux'])
            || !isset($data['observation']) || !isset($data['date_prevus']) || !isset($data['prelevement_par'])
            || !isset($data['Compression']) || !isset($data['Traction']) || !isset($data['Lieux_ouvrage'])
            || !isset($data['Traction_fend'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Please provide all required fields"]);
            return;
        }
        $intervention = Intervention::get($data['intervention_id']);
        if ($intervention['technicien_id'] != $user_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }

        $response = Phase_projet::insertPreReception($data, $user_id);
        return $response;
    }
}
