<?php

class InterventionController
{
    // parameters neccessary are fromDate and toDate
    static public function InterventionsChefRec()
    {
        $data = json_decode(file_get_contents("php://input"));

        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';

        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            return json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
        }
        $interventions = Intervention::getAll($fromDate, $toDate);
        if ($interventions == -1) {
            http_response_code(404);
            return json_encode(["message" => "No interventions found."]);
        }
        http_response_code(200);
        return json_encode($interventions);
    }
    // parameters neccessary are technicien_id and date
    static public function InterventionsTechnicien()
    {
        $data = json_decode(file_get_contents("php://input"));

        $technicien_id = $data->technicien_id ?? '';
        $date = $data->date ?? '';

        if (empty($technicien_id) || empty($date)) {
            http_response_code(400);
            return json_encode(["message" => "technicien_id and date are required.", "error" => "invalid data"]);
        }
        $interventions = Intervention::getAllTechnicien($technicien_id, $date);
        if ($interventions == -1) {
            http_response_code(404);
            return json_encode(["message" => "No interventions found."]);
        }
        http_response_code(200);
        return json_encode($interventions);
    }
    // parameters neccessary are intervention_id
    static public function Intervention()
    {
        $data = json_decode(file_get_contents("php://input"));

        $intervention_id = $data->intervention_id ?? '';

        if (empty($intervention_id)) {
            http_response_code(400);
            return json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::get($intervention_id);
        if ($intervention == -1) {
            http_response_code(404);
            return json_encode(["message" => "No intervention found."]);
        }
        http_response_code(200);
        return json_encode($intervention);
    }
    // parameters neccessary are technicien_id, projet_id, date_intervention, created_by
    static public function insertInterventionChef()
    {
        $data = json_decode(file_get_contents("php://input"));
        $technicien_id = $data->technicien_id ?? '';
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $etat = 1;
        $created_by = $data->created_by ?? '';
        if (empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($created_by)) {
            http_response_code(400);
            return json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(201);
        return json_encode(["message" => "Intervention created successfully."]);
    }
    // parameters neccessary are technicien_id, projet_id, date_intervention, created_by
    static public function insertInterventionTechnicien($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $etat = 0;
        $created_by = $data->created_by ?? '';
        if (empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($created_by)) {
            http_response_code(400);
            return json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(201);
        return json_encode(["message" => "Intervention created successfully."]);
    }
    // parameters neccessary are intervention_id and modifie_par
    static public function confirmIntervention($modifie_par)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        if (empty($intervention_id) || empty($modifie_par)) {
            http_response_code(400);
            return json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::confirmate($intervention_id, $modifie_par);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(200);
        return json_encode(["message" => "Intervention validated successfully."]);
    }
    // parameters neccessary are intervention_id and technicien_id
    static public function validateIntervention($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        if (empty($intervention_id)) {
            http_response_code(400);
            return json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $technicien_id) {
            http_response_code(401);
            return json_encode(["message" => "Unauthorized"]);
        }
        $intervention = Intervention::validate($intervention_id);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(200);
        return json_encode(["message" => "Intervention validated successfully."]);
    }
    // parameters neccessary are intervention_id and obs and technicien_id
    static public function annulerIntervention($technicien_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        $obs = $data->obs ?? '';
        if (empty($intervention_id) || empty($obs)) {
            http_response_code(400);
            return json_encode(["message" => "intervention_id and obs are required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $technicien_id) {
            http_response_code(401);
            return json_encode(["message" => "Unauthorized"]);
        }
        $intervention = Intervention::annuler($intervention_id, $obs);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(200);
        return json_encode(["message" => "Intervention canceled successfully."]);
    }
    // parameters neccessary are fromDate and toDate
    static public function DemandesInterventions()
    {
        $data = json_decode(file_get_contents("php://input"));
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';

        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            return json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
        }
        $interventions = Intervention::getDemandesInterventions($fromDate, $toDate);
        if ($interventions == -1) {
            http_response_code(404);
            return json_encode(["message" => "No interventions found."]);
        }
        http_response_code(200);
        return json_encode($interventions);
    }
    // parameters neccessary are intervention_id and obs
    static public function rejectDemandeIntervention()
    {
        $data = json_decode(file_get_contents("php://input"));
        $intervention_id = $data->intervention_id ?? '';
        $obs = $data->obs ?? '';

        if (empty($intervention_id) || empty($obs)) {
            http_response_code(400);
            return json_encode(["message" => "intervention_id and obs are required.", "error" => "invalid data"]);
        }
        $intervention = Intervention::rejectDemandeIntervention($intervention_id, $obs);
        if ($intervention == -1) {
            http_response_code(500);
            return json_encode(["message" => "Database error."]);
        }
        http_response_code(200);
        return json_encode(["message" => "Intervention rejected successfully."]);
    }
}
