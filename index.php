<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$Home = new HomeController();
$Home->handleRequest();

class HomeController
{
    public function handleRequest()
    {

        // Handle the page request based on user type
        if (isset($_GET['page']) && $_GET['page'] != 'login') {
            // Validate JWT token and get user data
            $userData = checkToken();
            if (isset($userData['message'])) {
                // If there's an error message, send it back
                echo json_encode($userData);
                return;
            }
            $user_type = $userData['user_type'] ?? '';
            $user_tache = $userData['user_tache'] ?? '';
            $user_id = $userData['id'] ?? '';
            $IDAgence = $userData['IDAgence'] ?? '';
            $page = $_GET['page'];
            $this->dispatchPage($user_type, $page, $user_id, $user_tache, $IDAgence);
        } else {
            $this->dispatchPage(null, 'login', "", "", "");
        }
    }

    private function dispatchPage($user_type, $page, $user_id, $user_tache, $IDAgence)
    {
        switch ($user_type) {
            case 1:
                $this->handleChef($page, $user_id, $IDAgence);
                break;
            case 5:
                $this->handleChef($page, $user_id, $IDAgence);
                break;
            case 3:
                if ($user_tache == 2) {
                    $this->handleTechnicien($page, $user_id, $IDAgence);
                } elseif ($user_tache == 1) {
                    $this->handleReception($page, $user_id, $IDAgence);
                }
                break;
            default:
                if ($page == 'login') {
                    LoginController::login();
                } else {
                    echo "404 Not Found";
                }
        }
    }

    private function handleChef($page, $user_id, $IDAgence)
    {
        $pages = [
            'interventionsChef',
            "interventionChef",
            "addInterventionInterface",
            "AddIntervention",
            'DemandesInterventions',
            "ValidateDemandeIntervention",
            "RejectDemandeIntervention",
            "AcceptDemandeConge",
            "RejectDemandeConge",
            "Prereceptions",
            "Receptions",
            "PreReception",
            "Reception",
            "DemandesConges",
            "DemandeConge",
            "validatePreReception",
            "NoteFraisInterface",
            "addNoteFrais",
            "NewReceptionInterface",
            "UpdatePreReception"
        ];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsChef':
                    ChefInterfaceController::interventionsChef($IDAgence);
                    break;
                case 'interventionChef':
                    ChefInterfaceController::interventionChef();
                    break;
                case 'addInterventionInterface':
                    ChefInterfaceController::addInterventionInterface($IDAgence);
                    break;
                case "AddIntervention":
                    ChefInterfaceController::addInterventionAction($user_id);
                    break;
                case "ValidateDemandeIntervention":
                    ChefInterfaceController::ValidateDemandeIntervention($user_id);
                    break;
                case "RejectDemandeIntervention":
                    ChefInterfaceController::RejectDemandeIntervention($user_id);
                    break;
                case 'Prereceptions':
                    ChefInterfaceController::PreReceptionsChef($IDAgence);
                    break;
                case 'Receptions':
                    ChefInterfaceController::ReceptionsChef($IDAgence);
                    break;
                case 'PreReception':
                    ChefInterfaceController::getPreReception();
                    break;
                case 'Reception':
                    ChefInterfaceController::getReception();
                    break;
                case 'DemandesInterventions':
                    ChefInterfaceController::DemandesInterventions($IDAgence);
                    break;
                case 'DemandesConges':
                    ChefInterfaceController::DemandesConges($IDAgence);
                    break;
                case 'DemandeConge':
                    ChefInterfaceController::DemandeConge();
                    break;
                case "AcceptDemandeConge":
                    ChefInterfaceController::AcceptDemandeConge($user_id, $IDAgence);
                    break;
                case "RejectDemandeConge":
                    ChefInterfaceController::RejectDemandeConge($user_id, $IDAgence);
                    break;
                case "validatePreReception":
                    ChefInterfaceController::validatePreReception($user_id);
                    break;
                case "NoteFraisInterface":
                    TechnicienInterfaceController::NoteFraisInterface($user_id);
                    break;
                case "addNoteFrais":
                    TechnicienInterfaceController::addNoteFrais($user_id);
                    break;
                case "NewReceptionInterface":
                    ChefInterfaceController::NewReceptionInterface();
                    break;
                case "UpdatePreReception":
                    ChefInterfaceController::UpdatePreReception($user_id);
                    break;
                default:
                    echo "None";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleTechnicien($page, $user_id, $IDAgence)
    {
        $pages = [
            'Programme',
            "InterventionDetails",
            "addInterventionInterface",
            "addInterventionAction",
            "DemandesInterventionsTec",
            "NewReception",
            "NewReceptionInterface",
            'CongesInterface',
            "AddDemandeConge",
            "insertPV",
            "getPV",
            "getPVs",
            "annulerIntervention",
            "interventionsWithoutPV",
            "newPV",
            "demandesInterventionsTec",
            "NoteFraisInterface",
            "addNoteFrais",
            "addLocation",
            "interventionsNotDone"
        ];
        if (in_array($page, $pages)) {
            switch ($page) {
                case "newPV":
                    TechnicienInterfaceController::insertPV($user_id);
                    break;
                case 'CongesInterface':
                    TechnicienInterfaceController::CongesInterface($user_id);
                    break;
                case "AddDemandeConge":
                    TechnicienInterfaceController::AddDemandeConge($user_id);
                    break;
                case 'Programme':
                    TechnicienInterfaceController::Programme($user_id);
                    break;
                case 'demandesInterventionsTec':
                    TechnicienInterfaceController::demandesInterventions($user_id);
                    break;
                case 'InterventionDetails':
                    TechnicienInterfaceController::InterventionDetails($user_id);
                    break;
                case "addInterventionInterface":
                    TechnicienInterfaceController::addInterventionInterface($IDAgence);
                    break;
                case "addInterventionAction":
                    TechnicienInterfaceController::addInterventionAction($user_id);
                    break;
                case "annulerIntervention":
                    TechnicienInterfaceController::annulateIntervention($user_id);
                    break;
                case "getPV":
                    TechnicienInterfaceController::getPV($user_id);
                    break;
                case "getPVs":
                    TechnicienInterfaceController::getPVs($user_id);
                    break;
                case 'DemandesInterventionsTec':
                    TechnicienInterfaceController::DemandesInterventionsTec($user_id);
                    break;
                case 'NewReceptionInterface':
                    TechnicienInterfaceController::NewReceptionInterface($user_id);
                    break;
                case 'NewReception':
                    TechnicienInterfaceController::NewReception($user_id);
                    break;
                case "interventionsWithoutPV":
                    TechnicienInterfaceController::interventionsWithoutPV($user_id);
                    break;
                case "interventionsNotDone":
                    TechnicienInterfaceController::interventionsNotDone($user_id);
                    break;
                case "NoteFraisInterface":
                    TechnicienInterfaceController::NoteFraisInterface($user_id);
                    break;
                case "addNoteFrais":
                    TechnicienInterfaceController::addNoteFrais($user_id);
                    break;
                case "addLocation":
                    TechnicienInterfaceController::addLocation($user_id);
                    break;

                default:
                    echo "404 Not Found 2";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleReception($page, $user_id, $IDAgence)
    {
        $pages = [
            'interventionsRec',
            "Prereceptions",
            'CongesInterface',
            "AddDemandeConge",
            'Receptions',
            "PVs",
            "PreReception",
            "Reception",
            "validatePreReception",
            "NoteFraisInterface",
            "addNoteFrais",
            "NewReceptionInterface",
            "UpdatePreReception"
        ];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsRec':
                    ChefInterfaceController::interventionsChef($IDAgence);
                    break;
                case 'CongesInterface':
                    TechnicienInterfaceController::CongesInterface($user_id);
                    break;
                case "AddDemandeConge":
                    TechnicienInterfaceController::AddDemandeConge($user_id);
                    break;
                case 'Prereceptions':
                    ReceptionController::PrereceptionsRec();
                    break;
                case "PreReception":
                    ReceptionController::PreReception();
                    break;
                case "Receptions":
                    ReceptionController::ReceptionsRec();
                    break;
                case "Reception":
                    ReceptionController::Reception();
                    break;
                case 'PVs':
                    ReceptionController::PVs();
                    break;
                case "validatePreReception":
                    ChefInterfaceController::validatePreReception($user_id);
                    break;
                case "NoteFraisInterface":
                    TechnicienInterfaceController::NoteFraisInterface($user_id);
                    break;
                case "addNoteFrais":
                    TechnicienInterfaceController::addNoteFrais($user_id);
                    break;
                case "NewReceptionInterface":
                    ChefInterfaceController::NewReceptionInterface();
                    break;
                case "UpdatePreReception":
                    ChefInterfaceController::UpdatePreReception($user_id);
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleLabo($page, $user_id)
    {
        $pages = ['Essai', 'Conges'];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'Essai':
                    echo "Essai";
                    break;
                case 'Conges':
                    echo "Conges";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }
}

function checkToken()
{
    $headers = apache_request_headers();
    if (!isset($headers['authorization'])) {
        http_response_code(400);
        return ["message" => "Token is required. 1"];
    }

    $token = $headers['authorization'];
    $token = str_replace('Bearer ', '', $token);

    if (!$token) {
        http_response_code(400);
        return ["message" => "Token is required. 2"];
    }

    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        $cc = (array) $decoded->data; // Convert stdClass object to array
        return $cc;
    } catch (Exception $e) {
        http_response_code(200);
        return ["message" => "Access denied.", "error" => $e->getMessage()];
    }
}
