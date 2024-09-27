<?php
require_once('DB.php');
class Conge
{
    public static function getCongesDemandes($fromDate, $toDate, $IDAgence)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT Conge_personnel.*,Nature_conge.*,Personnel.Nom_personnel,Fonction_personnel.lib_fonction_person
                FROM Conge_personnel
                LEFT JOIN Nature_conge ON Nature_conge.IDNature_conge=Conge_personnel.IDNature_conge
                LEFT JOIN Personnel ON Personnel.IDPersonnel = Conge_personnel.IDPersonnel
                LEFT JOIN Fonction_personnel ON Fonction_personnel.IDFonction_personnel=Personnel.IDFonction_personnel
                WHERE Personnel.IDAgence=$IDAgence AND Conge_personnel.date_debut > " . $fromDate;
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conges;
    }
    public static function getCongesDemandesTec($user_id, $year)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT Conge_personnel.*,Nature_conge.*,Personnel.Nom_personnel
                FROM Conge_personnel
                LEFT JOIN Nature_conge ON Nature_conge.IDNature_conge=Conge_personnel.IDNature_conge
                LEFT JOIN Personnel ON Personnel.IDPersonnel = Conge_personnel.IDPersonnel
                WHERE Conge_personnel.annee=:year AND Conge_personnel.valide=0 AND conge_personnel.Non_accorde=0 AND Conge_personnel.IDPersonnel=:user_id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conges;
    }
    public static function getDemandeConge($conge_id)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT Conge_personnel.*,Nature_conge.*,Personnel.Nom_personnel
                FROM Conge_personnel
                LEFT JOIN Nature_conge ON Nature_conge.IDNature_conge=Conge_personnel.IDNature_conge
                LEFT JOIN Personnel ON Personnel.IDPersonnel = Conge_personnel.IDPersonnel
                WHERE Conge_personnel.IDConge_personnel=:conge_id";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':conge_id', $conge_id);
        $stmt->execute();
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conges;
    }
    public static function acceptConge($Conge_id, $IDPersonnel)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE Conge_personnel SET valide = 1,date_valide=SYSDATE,idpersonnel_valide=:IDPersonnel WHERE IDConge_personnel = :conge_id";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel, $Conge_id]);
        return $stmt->rowCount() > 0;
    }
    public static function refuseConge($Conge_id, $obs)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "UPDATE Conge_personnel SET Non_accorde=1, Motif = ? WHERE IDConge_personnel = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$obs, $Conge_id]);
        return $stmt->rowCount() > 0;
    }
    public static function getCongeHistorique($IDPersonnel, $year)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT Conge_personnel.*, Nature_conge.* 
        FROM Conge_personnel 
        LEFT JOIN Nature_conge ON Conge_personnel.IDNature_conge = Nature_conge.IDNature_conge 
        WHERE Conge_personnel.IDPersonnel = :user_id AND Conge_personnel.annee = :year AND Conge_personnel.valide = 1 
        --AND Conge_personnel.valide_siege = 1
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':user_id', $IDPersonnel);
        $stmt->bindParam(':year', $year);
        $stmt->execute([$IDPersonnel, $year]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conges;
    }
    public static function getDaysAvailable($IDPersonnel, $year)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Conge_personnel WHERE IDPersonnel = ? AND annee = ? AND Conge_personnel.valide = 1 
        --AND Conge_personnel.valide_siege = 1
        ";
        $stmt = $con->prepare($sql);
        $stmt->execute([$IDPersonnel, $year]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $days = 0;
        foreach ($conges as $conge) {
            $days += $conge['Nbj_ouvrable'];
        }
        return 30 - $days;
    }
    public static function getYears($IDPersonnel)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "SELECT DISTINCT(annee) FROM Conge_personnel WHERE IDPersonnel = :IDPersonnel";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':IDPersonnel', $IDPersonnel);
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $years;
    }
    public static function demandeConge($IDPersonnel, $dateDebut, $dateFin, $year, $jours_pris, $motifsconge_id, $obs)
    {
        $con = Database::getInstance()->getConnection();
        $sql = "INSERT INTO Conge_personnel (IDPersonnel, date_debut, date_fin, annee, Nbj_ouvrable, IDNature_conge,Observation) VALUES (:IDPersonnel, :dateDebut, :dateFin, :year, :jours_pris, :motifsconge_id, :obs)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':IDPersonnel', $IDPersonnel);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':jours_pris', $jours_pris);
        $stmt->bindParam(':motifsconge_id', $motifsconge_id);
        $stmt->bindParam(':obs', $obs);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
