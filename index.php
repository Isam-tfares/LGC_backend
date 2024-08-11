<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$Home = new HomeController();
$Home->handleRequest();

class HomeController
{
    public function handleRequest()
    {
        // Handle the page request based on user type
        if (isset($_GET['page']) && $_GET['page'] != 'login') {
            // Validate JWT token and get user data
            $userData = JWTMiddleware::validateToken();
            if (isset($userData['message'])) {
                // If there's an error message, send it back
                echo json_encode($userData);
                return;
            }

            $user_type = $userData['user_type'] ?? '';
            $user_id = $userData['id'] ?? '';
            $page = $_GET['page'];
            $this->dispatchPage($user_type, $page, $user_id);
        } else {
            $this->dispatchPage(null, 'login', "");
        }
    }

    private function dispatchPage($user_type, $page, $user_id)
    {
        switch ($user_type) {
            case 'chef':
                $this->handleChef($page, $user_id);
                break;
            case 'technicien':
                $this->handleTechnicien($page, $user_id);
                break;
            case 'reception':
                $this->handleReception($page, $user_id);
                break;
            case 'labo':
                $this->handleLabo($page, $user_id);
                break;
            default:
                if ($page == 'login') {
                    LoginController::login();
                } else {
                    echo "404 Not Found";
                }
        }
    }

    private function handleChef($page, $user_id)
    {
        $pages = ['interventionsChef', "interventionChef", "addInterventionInterface", "AddIntervention", 'DemandesInterventions', "ValidateDemandeIntervention", "RejectDemandeIntervention", "AcceptDemandeConge", "RejectDemandeConge", 'PrereceptionsChef', "ReceptionsChef", "DemandesConges"];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsChef':
                    ChefInterfaceController::interventionsChef();
                    break;
                case 'interventionChef':
                    ChefInterfaceController::interventionChef();
                    break;
                case 'addInterventionInterface':
                    ChefInterfaceController::addInterventionInterface();
                    break;
                case "AddIntervention":
                    ChefInterfaceController::addInterventionAction();
                    break;
                case "ValidateDemandeIntervention":
                    ChefInterfaceController::ValidateDemandeIntervention($user_id);
                    break;
                case "RejectDemandeIntervention":
                    ChefInterfaceController::RejectDemandeIntervention($user_id);
                    break;
                case 'PrereceptionsChef':
                    echo "PrereceptionsChef";
                    break;
                case 'ReceptionsChef':
                    echo "ReceptionsChef";
                    break;
                case 'DemandesInterventions':
                    ChefInterfaceController::DemandesInterventions();
                    break;
                case 'DemandesConges':
                    ChefInterfaceController::DemandesConges();
                    break;
                case "AcceptDemandeConge":
                    ChefInterfaceController::AcceptDemandeConge();
                    break;
                case "RejectDemandeConge":
                    ChefInterfaceController::RejectDemandeConge();
                    break;
                default:
                    echo "None";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleTechnicien($page, $user_id)
    {
        $pages = ['Programme', "InterventionDetails", "addInterventionInterface", "addInterventionAction", "DemandesInterventionsTec", "NewReception", 'CongesInterface', "AddDemandeConge"];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsTec':
                    echo "interventionsTec";
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
                case 'InterventionDetails':
                    TechnicienInterfaceController::InterventionDetails($user_id);
                    break;
                case "addInterventionInterface":
                    TechnicienInterfaceController::addInterventionInterface();
                    break;
                case "addInterventionAction":
                    TechnicienInterfaceController::addInterventionAction($user_id);
                    break;
                case 'DemandesInterventionsTec':
                    echo "DemandesInterventionsTec";
                    break;
                case 'NewReception':
                    echo "NewReception";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleReception($page, $user_id)
    {
        $pages = ['interventionsRec', "PrereceptionsRec", 'Conges', 'receptions'];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsRec':
                    echo "interventionsRec";
                    break;
                case 'Conges':
                    echo "Conges";
                    break;
                case 'receptions':
                    echo "receptions";
                    break;
                case 'PrereceptionsRec':
                    echo "PrereceptionsRec";
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
