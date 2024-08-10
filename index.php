<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');

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
            $page = $_GET['page'];
            $this->dispatchPage($user_type, $page);
        } else {
            $this->dispatchPage(null, 'login');
        }
    }

    private function dispatchPage($user_type, $page)
    {
        switch ($user_type) {
            case 'chef':
                $this->handleChef($page);
                break;
            case 'technicien':
                $this->handleTechnicien($page);
                break;
            case 'reception':
                $this->handleReception($page);
                break;
            case 'labo':
                $this->handleLabo($page);
                break;
            default:
                if ($page == 'login') {
                    echo LoginController::login();
                } else {
                    echo "404 Not Found";
                }
        }
    }

    private function handleChef($page)
    {
        $pages = ['interventionsChef', 'DemandesInterventions', 'PrereceptionsChef', "ReceptionsChef", "DemandesConges"];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsChef':
                    echo "interventionsChef";
                    break;
                case 'PrereceptionsChef':
                    echo "PrereceptionsChef";
                    break;
                case 'ReceptionsChef':
                    echo "ReceptionsChef";
                    break;
                case 'DemandesInterventions':
                    echo "DemandesInterventions";
                    break;
                case 'DemandesConges':
                    echo "DemandesConges";
                    break;
            }
        } else {
            echo "404 Not Found";
        }
    }

    private function handleTechnicien($page)
    {
        $pages = ['Programme', "DemandesInterventionsTec", "NewReception", 'Conges'];
        if (in_array($page, $pages)) {
            switch ($page) {
                case 'interventionsTec':
                    echo "interventionsTec";
                    break;
                case 'Conges':
                    echo "Conges";
                    break;
                case 'Programme':
                    echo "Programme";
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

    private function handleReception($page)
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

    private function handleLabo($page)
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
