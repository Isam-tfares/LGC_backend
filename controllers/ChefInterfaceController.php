<?php
class ChefInterfaceController
{
    public static function interventionsChef()
    {
        $interventions = InterventionController::InterventionsChefRec();
        http_response_code(200);
        echo json_encode($interventions);
    }
    public static function interventionChef()
    {
        $intervention = InterventionController::Intervention(null, "chef");
        http_response_code(200);
        echo json_encode($intervention);
    }
    public static function addInterventionInterface()
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $techniciens = User::getTechniciens();
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "techniciens" => $techniciens,
            "projects" => $projects
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function addInterventionAction()
    {
        $response = InterventionController::insertInterventionChef();
        http_response_code(200);
        echo json_encode($response);
    }
    public static function DemandesInterventions()
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $techniciens = User::getTechniciens();
        $demandes = InterventionController::DemandesInterventions();
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "techniciens" => $techniciens,
            "projects" => $projects,
            "demandes" => $demandes
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function ValidateDemandeIntervention($user_id)
    {
        $response = InterventionController::confirmIntervention($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function RejectDemandeIntervention($user_id)
    {
        $response = InterventionController::rejectDemandeIntervention($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function DemandesConges()
    {
        $conges = CongeController::getCongesDemandes();
        http_response_code(200);
        echo json_encode($conges);
    }
    public static function AcceptDemandeConge()
    {
        $response = CongeController::acceptConge();
        http_response_code(200);
        echo json_encode($response);
    }
    public static function RejectDemandeConge()
    {
        $response = CongeController::refuseConge();
        http_response_code(200);
        echo json_encode($response);
    }
}
