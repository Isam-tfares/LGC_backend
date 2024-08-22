<?php
class TechnicienInterfaceController
{
    public static function CongesInterface($user_id)
    {
        $years = CongeController::getYears($user_id);
        $days = CongeController::getDaysAvailable($user_id);
        $motifs = MotifsConges::get();
        $conges = CongeController::getCongeHistorique($user_id);
        $demandesConges = CongeController::getCongesDemandesTec($user_id);
        $data = [
            "years" => $years,
            "days" => $days,
            "motifs" => $motifs,
            "conges" => $conges,
            "demandesConges" => $demandesConges
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function AddDemandeConge($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed."]);
            return;
        }

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
    public static function demandesInterventions($user_id)
    {

        $interventions = InterventionController::DemandesOfInterventions($user_id);
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
        $techniciens = User::getTechniciens();
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "projects" => $projects,
            "techniciens" => $techniciens,
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function addInterventionAction($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed."]);
            return;
        }
        $response = InterventionController::insertInterventionTechnicien($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function annulateIntervention($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed."]);
            return;
        }
        $response = InterventionController::annulerIntervention($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function insertPV($user_id)
    {
        // Check if the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed."]);
            return;
        }
        PVController::insertPv($user_id);
    }
    public static function getPV($user_id)
    {
        $pv = PVController::getPV($user_id);
        http_response_code(200);
        echo json_encode($pv);
    }
    public static function getPVs($user_id)
    {
        $pvs = PVController::getPVsTec($user_id);
        http_response_code(200);
        echo json_encode($pvs);
    }


    public static function DemandesInterventionsTec($user_id)
    {
        $interventions = InterventionController::DemandesInterventionsTec($user_id);
        http_response_code(200);
        echo json_encode($interventions);
    }
    public static function NewReceptionInterface($user_id)
    {
        $clients = Client::getAll();
        $projects = Projet::getAll();
        $phases = Phase::get();
        $materiaux = Materiaux::getAll();
        $types_beton = BetonTypes::get();
        $natures_echantillon = EchantillonNatures::get();
        $interventions_not_done = InterventionController::getNotDoneInterventions($user_id);
        $data = [
            "clients" => $clients,
            "phases" => $phases,
            "projects" => $projects,
            "materiaux" => $materiaux,
            "types_beton" => $types_beton,
            "natures_echantillon" => $natures_echantillon,
            "interventions" => $interventions_not_done
        ];
        http_response_code(200);
        echo json_encode($data);
    }
    public static function NewReception($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed."]);
            return;
        }
        $response = Phase_projetController::newReceptionTec($user_id);
        http_response_code(200);
        echo json_encode($response);
    }
    public static function interventionsWithoutPV($user_id)
    {
        $interventions = Intervention::interventionsDoneWithoutPV($user_id);
        http_response_code(200);
        echo json_encode($interventions);
    }
}
