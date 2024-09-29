<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class ChefInterfaceController
{
    public static function interventionsChef($IDAgence)
    {
        $interventions = InterventionController::InterventionsChefRec($IDAgence);
        http_response_code(200);
        echo json_encode($interventions);
    }
    public static function interventionChef()
    {
        $intervention = InterventionController::Intervention(null, "chef");
        http_response_code(200);
        echo json_encode($intervention);
    }
    public static function addInterventionInterface($IDAgence)
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $techniciens = User::getTechniciens($IDAgence);
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "techniciens" => $techniciens,
            "projects" => $projects
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function addInterventionAction($user_id)
    {
        $response = InterventionController::insertInterventionChef($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function DemandesInterventions($IDAgence)
    {
        $demandes = InterventionController::DemandesInterventions($IDAgence);
        $data = $demandes;
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
    public static function DemandesConges($IDAgence)
    {
        $conges = CongeController::getCongesDemandes($IDAgence);
        http_response_code(200);
        echo json_encode($conges);
    }
    public static function DemandeConge()
    {
        $conge = CongeController::getDemandeConge();
        http_response_code(200);
        echo json_encode($conge);
    }
    public static function AcceptDemandeConge($IDPersonnel, $IDAgence)
    {
        $response = CongeController::acceptConge($IDPersonnel, $IDAgence);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function RejectDemandeConge($user_id, $IDAgence)
    {
        $response = CongeController::refuseConge($user_id, $IDAgence);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function PrereceptionsChef($IDAgence)
    {
        $preReceptions = Phase_projetController::PreReceptionsChef($IDAgence);
        http_response_code(200);
        echo json_encode($preReceptions);
    }
    public static function ReceptionsChef($IDAgence)
    {
        $receptions = Phase_projetController::ReceptionsChef($IDAgence);
        http_response_code(200);
        echo json_encode($receptions);
    }
    public static function getReception()
    {
        $reception = Phase_projetController::getReceptionByIntervention();
        http_response_code(200);
        echo json_encode($reception);
    }
    public static function getPreReception()
    {
        $reception = Phase_projetController::getPreReceptionByIntervention();
        http_response_code(200);
        echo json_encode($reception);
    }
    public static function UpdatePreReception($user_id)
    {
        $response = Phase_projetController::UpdatePreReception($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function validatePrereception($user_id)
    {
        $response = Phase_projetController::validatePreReception($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function NewReceptionInterface()
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $materiaux = Materiaux::getAll();
        $types_beton = BetonTypes::get();
        $natures_echantillon = EchantillonNatures::get();
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "projects" => $projects,
            "materiaux" => $materiaux,
            "types_beton" => $types_beton,
            "natures_echantillon" => $natures_echantillon
        ];
        http_response_code(200);
        echo json_encode($data);
    }
}
