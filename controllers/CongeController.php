<?php
class CongeController
{
    // parameters necessary year and IDPersonnel
    public function getCongeHistorique()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data->year ?? '';
        $IDPersonnel = $data->IDPersonnel ?? '';
        if (empty($year) || empty($IDPersonnel)) {
            http_response_code(400);
            return json_encode(["message" => "year and IDPersonnel are required.", "error" => "invalid data"]);
        }
        $conges = Conge::getCongeHistorique($IDPersonnel, $year);
        if ($conges == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conges found."]);
        }
        http_response_code(200);
        return json_encode($conges);
    }
    // parameters necessary are IDPersonnel, year
    public function getDaysAvailable()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $IDPersonnel = $data->IDPersonnel ?? '';
        $year = $data->year ?? '';
        if (empty($IDPersonnel) || empty($year)) {
            http_response_code(400);
            return json_encode(["message" => "IDPersonnel and year are required.", "error" => "invalid data"]);
        }
        $days = Conge::getDaysAvailable($IDPersonnel, $year);
        if ($days == -1) {
            http_response_code(404);
            return json_encode(["message" => "No days found."]);
        }
        http_response_code(200);
        return json_encode($days);
    }
    // parameters necessary are IDPersonnel
    public static function getYears()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $IDPersonnel = $data->IDPersonnel ?? '';
        if (empty($IDPersonnel)) {
            http_response_code(400);
            return json_encode(["message" => "IDPersonnel is required.", "error" => "invalid data"]);
        }
        $years = Conge::getYears($IDPersonnel);
        if ($years == -1) {
            http_response_code(404);
            return json_encode(["message" => "No years found."]);
        }
        http_response_code(200);
        return json_encode($years);
    }
    // parameters necessary are IDPersonnel, dateDebut, dateFin,year,jours_pris,motifsconge_id
    public static function demandeConge()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $IDPersonnel = $data->IDPersonnel ?? '';
        $dateDebut = $data->dateDebut ?? '';
        $dateFin = $data->dateFin ?? '';
        $year = $data->year ?? '';
        $jours_pris = $data->jours_pris ?? '';
        $motifsconge_id = $data->motifsconge_id ?? '';
        $etat_demande = 0;
        if (empty($IDPersonnel) || empty($dateDebut) || empty($dateFin) || empty($year) || empty($jours_pris) || empty($motifsconge_id)) {
            http_response_code(400);
            return json_encode(["message" => "IDPersonnel, dateDebut, dateFin, year, jours_pris and motifsconge_id are required.", "error" => "invalid data"]);
        }
        $conge = Conge::demandeConge($IDPersonnel, $dateDebut, $dateFin, $year, $jours_pris, $motifsconge_id, $etat_demande);
        if ($conge == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conge found."]);
        }
        http_response_code(200);
        return json_encode($conge);
    }
    // parameters necessary are fromDate,toDate
    public static function getCongesDemandes()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            return json_encode(["message" => "fromDate and toDate are required.", "error" => "invalid data"]);
        }
        $conges = Conge::getCongesDemandes($fromDate, $toDate);
        if ($conges == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conges found."]);
        }
        http_response_code(200);
        return json_encode($conges);
    }
    // parameters necessary are Conge_id IDPersonnel
    public static function acceptConge()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $Conge_id = $data->Conge_id ?? '';
        $IDPersonnel = $data->IDPersonnel ?? '';
        if (empty($Conge_id) || empty($IDPersonnel)) {
            http_response_code(400);
            return json_encode(["message" => "Conge_id and IDPersonnel are required.", "error" => "invalid data"]);
        }
        $conge = Conge::acceptConge($Conge_id, $IDPersonnel);
        if ($conge == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conge found."]);
        }
        http_response_code(200);
        return json_encode($conge);
    }
    // parameters necessary are Conge_id IDPersonnel obs
    public static function refuseConge()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $Conge_id = $data->Conge_id ?? '';
        $IDPersonnel = $data->IDPersonnel ?? '';
        $obs = $data->obs ?? '';
        if (empty($Conge_id) || empty($IDPersonnel) || empty($obs)) {
            http_response_code(400);
            return json_encode(["message" => "Conge_id, IDPersonnel and obs are required.", "error" => "invalid data"]);
        }
        $conge = Conge::refuseConge($Conge_id, $obs);
        if ($conge == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conge found."]);
        }
        http_response_code(200);
        return json_encode($conge);
    }
}
