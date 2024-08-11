<?php
class CongeController
{
    // parameters necessary year
    public static function getCongeHistorique($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $year = $data->year ?? '';
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
    // parameters necessary  year
    public static function getDaysAvailable($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
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
    // parameters necessary are None
    public static function getYears($IDPersonnel)
    {
        $years = Conge::getYears($IDPersonnel);
        if ($years == -1) {
            http_response_code(404);
            return json_encode(["message" => "No years found."]);
        }
        http_response_code(200);
        return json_encode($years);
    }
    // parameters necessary are fromDate, toDate,year,nbr_days,motifsconge_id,autreMotif
    public static function demandeConge($IDPersonnel)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $fromDate = $data->fromDate ?? '';
        $toDate = $data->toDate ?? '';
        $year = $data->year ?? '';
        $nbr_days = $data->nbr_days ?? '';
        $motifsconge_id = $data->motifsconge_id ?? '';
        $autreMotif = $data->autreMotif ?? '';
        if (empty($IDPersonnel) || empty($fromDate) || empty($toDate) || empty($year) || empty($nbr_days) || empty($motifsconge_id)) {
            http_response_code(400);
            echo json_encode(["message" => "IDPersonnel, fromDate, toDate, year, nbr_days and motifsconge_id are required.", "error" => "invalid data"]);
        }
        $conge = Conge::demandeConge($IDPersonnel, $fromDate, $toDate, $year, $nbr_days, $motifsconge_id, $autreMotif);
        if ($conge == -1) {
            http_response_code(404);
            echo json_encode(["message" => "No conge found."]);
        }
        return $conge;
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
            echo json_encode(["message" => "No conges found."]);
        }
        http_response_code(200);
        return $conges;
    }
    // parameters necessary are conge_id IDPersonnel
    public static function acceptConge()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $conge_id = $data->conge_id ?? '';
        $IDPersonnel = $data->IDPersonnel ?? '';
        if (empty($conge_id) || empty($IDPersonnel)) {
            http_response_code(400);
            return json_encode(["message" => "conge_id and IDPersonnel are required.", "error" => "invalid data"]);
        }
        $conge = Conge::acceptConge($conge_id, $IDPersonnel);
        if ($conge == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conge found."]);
        }
        http_response_code(200);
        return json_encode($conge);
    }
    // parameters necessary are conge_id IDPersonnel obs
    public static function refuseConge()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $conge_id = $data->conge_id ?? '';
        $IDPersonnel = $data->IDPersonnel ?? '';
        $obs = $data->obs ?? '';
        if (empty($conge_id) || empty($IDPersonnel) || empty($obs)) {
            http_response_code(400);
            return json_encode(["message" => "conge_id, IDPersonnel and obs are required.", "error" => "invalid data"]);
        }
        $conge = Conge::refuseConge($conge_id, $obs);
        if ($conge == -1) {
            http_response_code(404);
            return json_encode(["message" => "No conge found."]);
        }
        http_response_code(200);
        return json_encode($conge);
    }
}
