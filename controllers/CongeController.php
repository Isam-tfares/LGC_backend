<?php
class CongeController
{
    // parameters necessary year
    public static function getCongeHistorique($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data['year'] ?? '';
        if (empty($year) || empty($IDPersonnel)) {
            http_response_code(400);
            echo json_encode(["message" => "year and IDPersonnel are required.", "error" => "invalid data"]);
            return;
        }
        $conges = Conge::getCongeHistorique($IDPersonnel, $year);
        return $conges;
    }
    // parameters necessary  year
    public static function getDaysAvailable($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data['year'] ?? '';
        if (empty($IDPersonnel) || empty($year)) {
            http_response_code(400);
            echo json_encode(["message" => "IDPersonnel and year are required.", "error" => "invalid data"]);
            return;
        }
        $days = Conge::getDaysAvailable($IDPersonnel, $year);
        return $days;
    }
    // parameters necessary are None
    public static function getYears($IDPersonnel)
    {
        $years = Conge::getYears($IDPersonnel);
        $currentYear = date('Y');

        // Check if the current year exists in the array
        $yearExists = false;
        foreach ($years as $year) {
            if ($year['annee'] == $currentYear) {
                $yearExists = true;
                break;
            }
        }

        // If the current year is not in the array, add it to the beginning
        if (!$yearExists) {
            array_unshift($years, ["annee" => $currentYear]);
        } else {
            // Move the current year to the first position if it exists
            foreach ($years as $key => $year) {
                if ($year['annee'] == $currentYear) {
                    unset($years[$key]); // Remove it from its current position
                    array_unshift($years, ["annee" => $currentYear]); // Add it to the beginning
                    break;
                }
            }
        }

        return array_values($years); // Reindex the array
    }


    // parameters necessary are fromDate, toDate,year,nbr_days,motifsconge_id
    public static function demandeConge($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $fromDate = $data['fromDate'] ?? '';
        $toDate = $data['toDate'] ?? '';
        $year = $data['year'] ?? '';
        $nbr_days = $data['nbr_days'] ?? '';
        $motifsconge_id = $data['motifsconge_id'] ?? '';
        $obs = $data['obs'] ?? '';
        if (empty($IDPersonnel) || empty($fromDate) || empty($toDate) || empty($year) || empty($nbr_days) || empty($motifsconge_id)) {
            http_response_code(400);
            echo json_encode(["message" => "IDPersonnel, fromDate, toDate, year, nbr_days and motifsconge_id are required.", "error" => "invalid data"]);
            return;
        }
        $conge = Conge::demandeConge($IDPersonnel, $fromDate, $toDate, $year, $nbr_days, $motifsconge_id, $obs);
        return $conge;
    }
    // parameters necessary are fromDate,toDate
    public static function getCongesDemandes($IDAgence)
    {
        // Read and decode the input JSON
        $data = json_decode(file_get_contents('php://input'), true);

        $fromDate = $data['fromDate'] ?? '';
        $toDate = $data['toDate'] ?? '';

        // Validate the date parameters
        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            echo json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
            return; // Exit the function to prevent further processing
        }

        // Call the method to get congés demandes
        $conges = Conge::getCongesDemandes($fromDate, $toDate, $IDAgence);
        return $conges;
    }
    public static function getCongesDemandesTec($user_id)
    {
        // Read and decode the input JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data['year'] ?? '';
        // Validate the date parameters
        if (empty($year)) {
            http_response_code(400);
            echo json_encode(["message" => "year is required.", "error" => "invalid data"]);
            return; // Exit the function to prevent further processing
        }
        // Call the method to get congés demandes
        $conges = Conge::getCongesDemandesTec($user_id, $year);
        return $conges;
    }
    public static function getDemandesRefus($user_id)
    {
        // Read and decode the input JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data['year'] ?? '';
        // Validate the date parameters
        if (empty($year)) {
            http_response_code(400);
            echo json_encode(["message" => "year is required.", "error" => "invalid data"]);
            return; // Exit the function to prevent further processing
        }
        // Call the method to get congés demandes
        $conges = Conge::getDemandesRefus($user_id, $year);
        return $conges;
    }
    public static function getDemandeConge()
    {
        // Read and decode the input JSON
        $data = json_decode(file_get_contents('php://input'), true);

        $conge_id = $data['conge_id'] ?? '';

        // Validate the date parameters
        if (empty($conge_id)) {
            http_response_code(400);
            echo json_encode(["message" => "conge_id is required.", "error" => "invalid data"]);
            return; // Exit the function to prevent further processing
        }

        // Call the method to get congés demandes
        $conge = Conge::getDemandeConge($conge_id);
        return $conge;
    }

    // parameters necessary are conge_id IDPersonnel
    public static function acceptConge($IDPersonnel, $IDAgence)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $conge_id = $data['conge_id'] ?? '';
        if (empty($conge_id)) {
            http_response_code(400);
            echo json_encode(["message" => "conge_id is required.", "error" => "invalid data"]);
            return;
        }
        $conge = Conge::acceptConge($conge_id, $IDPersonnel, $IDAgence);
        return $conge;
    }
    // parameters necessary are conge_id IDPersonnel obs
    public static function refuseConge($user_id, $IDAgence)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $conge_id = $data['conge_id'] ?? '';
        $obs = $data['obs'] ?? '';
        if (empty($conge_id) || empty($obs)) {
            http_response_code(400);
            echo json_encode(["message" => "conge_id, IDPersonnel and obs are required.", "error" => "invalid data"]);
            return;
        }
        $conge = Conge::refuseConge($user_id, $conge_id, $obs, $IDAgence);
        return $conge;
    }
}
