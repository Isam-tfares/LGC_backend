<?php
class TechnicienInterfaceController
{
    public static function CongesInterface($user_id)
    {
        $years = CongeController::getYears($user_id);
        $days = CongeController::getDaysAvailable($user_id);
        $motifs = MotifsConges::get();
        $conges = CongeController::getCongeHistorique($user_id);
        $data = [
            "years" => $years,
            "days" => $days,
            "motifs" => $motifs,
            "conges" => $conges,
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function AddDemandeConge($user_id)
    {

        $response = CongeController::demandeConge($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function Programme($user_id)
    {

        $interventions = InterventionController::InterventionsTechnicien($user_id);
        http_response_code(200);
        echo json_encode($interventions);
    }
    public static function InterventionDetails($user_id)
    {

        $intervention = InterventionController::Intervention($user_id);
        http_response_code(200);
        echo json_encode($intervention);
    }
    public static function addInterventionInterface()
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "projects" => $projects
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function addInterventionAction($user_id)
    {
        $response = InterventionController::insertInterventionTechnicien($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
}
