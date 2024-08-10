<?php
require_once('DB.php');
class Conge
{
    public static function getCongesDemandes($fromDate, $toDate)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM conges.*, motifsconge.* FROM conges JOIN motifsconge ON conges.motifsconge_id = motifsconge.motifsconge_id WHERE conges.date_demande BETWEEN ? AND ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$fromDate, $toDate]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($conges)) {
            return -1;
        }
        return $conges;
    }
    public static function acceptConge($Conge_id, $IDPersonnel)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE conges SET etat_demande = 1 WHERE conge_id = ? AND user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$Conge_id, $IDPersonnel]);
        if ($stmt->rowCount() == 0) {
            return -1;
        }
        return 1;
    }
    public static function refuseConge($Conge_id, $obs)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE conge SET etat_demande=2, obs = ? WHERE conge_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$obs, $Conge_id]);
        if ($stmt->rowCount() == 0) {
            return -1;
        }
        return 1;
    }
    public static function getCongeHistorique($IDPersonnel, $year)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM conges.*, motifsconge.* FROM conges JOIN motifsconge ON conges.motifsconge_id = motifsconge.motifsconge_id WHERE conges.user_id = ? AND conges.annee = ? AND conges.etat_demande = 1";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel, $year]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($conges)) {
            return -1;
        }
        return $conges;
    }
    public static function getDaysAvailable($IDPersonnel, $year)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM conges WHERE user_id = ? AND annee = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel, $year]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($conges)) {
            return -1;
        }
        $days = 0;
        foreach ($conges as $conge) {
            $days += $conge['jours_pris'];
        }
        return 30 - $days;
    }
    public static function getYears($IDPersonnel)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT DISTINCT annee FROM conges WHERE user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel]);
        $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($years)) {
            return -1;
        }
        return $years;
    }
    public static function demandeConge($IDPersonnel, $dateDebut, $dateFin, $year, $jours_pris, $motifsconge_id, $etat_demande)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "INSERT INTO conges (user_id, date_debut, date_fin, annee, jours_pris, motifsconge_id, etat_demande) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel, $dateDebut, $dateFin, $year, $jours_pris, $motifsconge_id, $etat_demande]);
        if ($stmt->rowCount() == 0) {
            return -1;
        }
        return 1;
    }
}
