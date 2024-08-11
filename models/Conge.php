<?php
require_once('DB.php');
class Conge
{
    public static function getCongesDemandes($fromDate, $toDate)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT conges.*,motifsconge.*,Personnel.Nom_personnel
                FROM conges
                INNER JOIN motifsconge ON motifsconge.motifsconge_id=conges.motifsconge_id
                INNER JOIN Personnel ON Personnel.IDPersonnel = conges.user_id
                WHERE conges.date_demande BETWEEN " . $fromDate . " AND " . $toDate;
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($conges)) {
            return -1;
        }
        return $conges;
    }
    public static function acceptConge($Conge_id, $IDPersonnel)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE conges SET etat_demande = 2,date_modification=SYSDATE WHERE conge_id = :conge_id";
        $stmt = $con->prepare($sql);
        $stmt->execute([$Conge_id]);
        if ($stmt->rowCount() == 0) {
            return -1;
        }
        return 1;
    }
    public static function refuseConge($Conge_id, $obs)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE conges SET etat_demande=0,date_modification=SYSDATE, obs = ? WHERE conge_id = ?";
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
        $sql = "SELECT * FROM conges.*, motifsconge.* FROM conges JOIN motifsconge ON conges.motifsconge_id = motifsconge.motifsconge_id WHERE conges.user_id = ? AND conges.annee = ? AND conges.etat_demande = 2";
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
        $sql = "SELECT DISTINCT(annee) FROM conges WHERE user_id = :IDPersonnel";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':IDPersonnel', $IDPersonnel);
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($years)) {
            return -1;
        }
        return $years;
    }
    public static function demandeConge($IDPersonnel, $dateDebut, $dateFin, $year, $jours_pris, $motifsconge_id, $autreMotif)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "INSERT INTO conges (user_id, start_date, end_date, annee, jours_pris, motifsconge_id, autre_motif,date_demande) VALUES (:IDPersonnel, :dateDebut, :dateFin, :year, :jours_pris, :motifsconge_id,:autreMotif, SYSDATE)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':IDPersonnel', $IDPersonnel);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':jours_pris', $jours_pris);
        $stmt->bindParam(':motifsconge_id', $motifsconge_id);
        $stmt->bindParam(':autreMotif', $autreMotif);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return -1;
        }
        return 1;
    }
}
