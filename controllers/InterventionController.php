<?php

class InterventionController
{
    // parameters neccessary are fromDate and toDate
    static public function InterventionsChefRec()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $fromDate = $data['fromDate'] ?? '';
        $toDate = $data['toDate'] ?? '';
        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            echo json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
            return;
        }

        $interventions = Intervention::getAll($fromDate, $toDate);
        if ($interventions == -1) {
            http_response_code(404);
            echo json_encode(["message" => "No interventions found."]);
            return;
        }

        return $interventions;
    }
    // parameters neccessary are technicien_id and date
    static public function InterventionsTechnicien($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $date = $data->date ?? '';

        if (empty($date)) {
            $date = date('Ymd');
        }
        $interventions = Intervention::getAllTechnicien($technicien_id, $date);
        if ($interventions == -1) {
            http_response_code(404);
            echo json_encode(["message" => "No interventions found."]);
            return;
        }

        return $interventions;
    }
    // parameters neccessary are intervention_id
    static public function Intervention($user_id, $role = null)
    {
        $data = json_decode(file_get_contents("php://input"));

        $intervention_id = $data->intervention_id ?? '';

        if (empty($intervention_id)) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::get($intervention_id);
        if ($role != "chef" && $intervention['technicien_id'] != $user_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }
        if ($intervention == -1) {
            http_response_code(404);
            echo json_encode(["message" => "No intervention found."]);
            return;
        }

        return $intervention;
    }
    // parameters neccessary are technicien_id, projet_id, date_intervention, created_by
    static public function insertInterventionChef()
    {
        $data = json_decode(file_get_contents("php://input"));
        $technicien_id = $data->technicien_id ?? '';
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $IDPhase = $data->IDPhase ?? '';
        $etat = 1;
        $created_by = $data->created_by ?? '';
        if (empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($created_by)) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by, $IDPhase);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }
        return ["message" => "Intervention created successfully."];
    }
    // parameters neccessary are technicien_id, projet_id, date_intervention, created_by
    static public function insertInterventionTechnicien($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $etat = 0;
        $created_by = $technicien_id;
        $IDPhase = $data->IDPhase ?? '';
        if (empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($created_by)) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by, $IDPhase);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }

        return ["message" => "Intervention created successfully."];
    }
    // parameters neccessary are intervention_id and modifie_par
    static public function confirmIntervention($modifie_par)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        $technicien_id = $data->technicien_id ?? '';
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $IDPhase = $data->IDPhase ?? '';
        if (empty($intervention_id) || empty($modifie_par) || empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($IDPhase)) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::confirmate($intervention_id, $modifie_par, $technicien_id, $projet_id, $date_intervention, $IDPhase);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }

        return ["message" => "Intervention confimate successfully."];
    }
    // parameters neccessary are intervention_id and technicien_id
    static public function validateIntervention($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        if (empty($intervention_id)) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $technicien_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }

        $intervention = Intervention::validate($intervention_id);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }


        return ["message" => "Intervention validated successfully."];
    }
    // parameters neccessary are intervention_id and obs and technicien_id
    static public function annulerIntervention($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        $obs = $data->obs ?? '';
        if (empty($intervention_id) || empty($obs)) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id and obs are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $technicien_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }

        $intervention = Intervention::annuler($intervention_id, $obs);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }


        return ["message" => "Intervention canceled successfully."];
    }
    // parameters neccessary are fromDate and toDate
    static public function DemandesInterventions()
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';

        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            echo json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
            return;
        }

        $interventions = Intervention::getDemandesInterventions($fromDate, $toDate);
        if ($interventions == -1) {
            http_response_code(404);
            echo json_encode(["message" => "No interventions found."]);
            return;
        }
        return $interventions;
    }
    // parameters neccessary are intervention_id and obs
    static public function rejectDemandeIntervention($user_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        $obs = $data->obs ?? '';

        if (empty($intervention_id) || empty($obs)) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id and obs are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::rejectDemandeIntervention($intervention_id, $obs, $user_id);
        if ($intervention == -1) {
            http_response_code(500);
            echo json_encode(["message" => "Database error."]);
            return;
        }

        return ["message" => "Intervention rejected successfully."];
    }
}
