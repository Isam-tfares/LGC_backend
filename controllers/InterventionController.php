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
            echo json_encode(["error" => "fromDate and toDate are required."]);
            return;
        }

        $interventions = Intervention::getAll($fromDate, $toDate);
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
        return $interventions;
    }
    static public function DemandesOfInterventions($technicien_id)
    {
        $interventions = Intervention::getDemandesTec($technicien_id);
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
        if (isset($intervention['technicien_id']) && $role != "chef" && $intervention['technicien_id'] != $user_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }
        return $intervention;
    }
    // parameters neccessary are technicien_id, projet_id, date_intervention,IDPhase
    static public function insertInterventionChef($user_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $technicien_id = $data->technicien_id ?? '';
        $projet_id = $data->projet_id ?? '';
        $date_intervention = $data->date_intervention ?? '';
        $IDPhase = $data->IDPhase ?? '';
        $etat = 1;
        $created_by = $user_id;
        if (empty($technicien_id) || empty($projet_id) || empty($date_intervention) || empty($created_by)) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required.", "error" => "invalid data"]);
            return;
        }

        $intervention = Intervention::insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by, $IDPhase);
        return  $intervention;
    }
    // parameters neccessary are  projet_id, date_intervention, IDPhase
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
        return $intervention;
    }
    // parameters neccessary are intervention_id, technicien_id, projet_id, date_intervention, IDPhase
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
        return $intervention;
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
        return $intervention;
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
        return $intervention;
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
        return $intervention;
    }
    public static function DemandesInterventionsTec($user_id)
    {
        $interventions = Intervention::getDemandesInterventionsTec($user_id);
        return $interventions;
    }
    public static function getNotDoneInterventions($user_id)
    {
        $interventions = Intervention::getNotDoneInterventions($user_id);
        return $interventions;
    }
    public static function updateInterventionState($intervention_id, $user_id)
    {
        if (empty($intervention_id)) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id is required.", "error" => "invalid data"]);
            return;
        }
        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $user_id) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }
        $intervention = Intervention::updateState($intervention_id);
    }
}
