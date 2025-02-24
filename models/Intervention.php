<?php
require_once('DB.php');
class Intervention
{
    static public function getAll($fromDate, $toDate, $IDAgence)
    {
        try {
            if ($IDAgence == 4) {
                $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle,Agence.libelle AS agence_name
                FROM interventions
                INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
                INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
                INNER JOIN Client ON Projet.IDClient=Client.IDClient 
                INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
                INNER JOIN Agence ON Agence.IDAgence=Personnel.IDAgence
                WHERE interventions.etat_confirmation=1 
                AND date_intervention Between " . $fromDate . " and " . $toDate . " 
                ORDER BY interventions.date_intervention DESC");
            } else {
                $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
                FROM interventions
                INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
                INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
                INNER JOIN Client ON Projet.IDClient=Client.IDClient 
                INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
                WHERE interventions.etat_confirmation=1 
                AND Personnel.IDAgence=$IDAgence
                AND date_intervention Between " . $fromDate . " and " . $toDate . " 
                ORDER BY interventions.date_intervention DESC");
            }
            $stm->execute();

            // Check if there were any errors during execution
            if ($stm->errorCode() != '00000') {
                $errorInfo = $stm->errorInfo();
                throw new Exception("SQL Error: " . $errorInfo[2]);
            }

            $res = $stm->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC to get an associative array
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getAllNotdone($fromDate, $toDate, $user_id)
    {
        $user_id = (int)$user_id;
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.X,Projet.Y,Projet.IDProjet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
                FROM interventions
                INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
                INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
                INNER JOIN Client ON Projet.IDClient=Client.IDClient 
                INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
                WHERE interventions.etat_confirmation=1 
                AND Personnel.IDPersonnel=" . $user_id . "
                AND interventions.status=1
                AND interventions.date_intervention Between " . $fromDate . " and " . $toDate . " 
                ORDER BY interventions.date_intervention DESC");

            $stm->execute();

            // Check if there were any errors during execution
            if ($stm->errorCode() != '00000') {
                $errorInfo = $stm->errorInfo();
                throw new Exception("SQL Error: " . $errorInfo[2]);
            }

            $res = $stm->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC to get an associative array
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getAllTechnicien($technicien_id, $date)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Projet.X,Projet.Y,Projet.IDProjet,Client.abr_client,Phase.libelle
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE interventions.etat_confirmation=1 
            AND technicien_id=:technicien_id AND date_intervention=" . $date . "
             ORDER BY interventions.date_intervention DESC");
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getDemandesTec($technicien_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE interventions.etat_confirmation=0
            AND technicien_id=:technicien_id
            ORDER BY interventions.date_intervention DESC");
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function get($intervention_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
            FROM interventions 
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel 
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet 
            INNER JOIN Client ON Projet.IDClient=Client.IDClient 
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->execute();

            $res = $stm->fetch();
            $res = Database::encode_utf8([$res])[0];
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by, $IDPhase, $Lieux_ouvrage)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("INSERT INTO interventions 
            (technicien_id,projet_id,date_intervention,etat_confirmation,cree_par,date_creation,IDPhase,status,Lieux_ouvrage)
            VALUES (:technicien_id,:projet_id,:date_intervention,:etat_confirmation,:cree_par,SYSDATE,:IDPhase,1,:Lieux_ouvrage)");
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->bindParam(':projet_id', $projet_id);
            $stm->bindParam(':date_intervention', $date_intervention);
            $stm->bindParam(':etat_confirmation', $etat);
            $stm->bindParam(':cree_par', $created_by);
            $stm->bindParam(':IDPhase', $IDPhase);
            $stm->bindParam(':Lieux_ouvrage', $Lieux_ouvrage);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function update($intervention_id, $technicien_id, $projet_id, $date_intervention, $status = 1, $obs = "")
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET technicien_id=:technicien_id,projet_id=:projet_id,
            date_intervention=TO_DATE(:date_intervention, 'DD/MM/YYYY'),status=:status,obs=:obs,
            date_modification=NOW()
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->bindParam(':projet_id', $projet_id);
            $stm->bindParam(':date_intervention', $date_intervention);
            $stm->bindParam(':status', $status);
            $stm->bindParam(':obs', $obs);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function confirmate($intervention_id, $modifie_par, $technicien_id, $projet_id, $date_intervention, $IDPhase, $Lieux_ouvrage)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET etat_confirmation=1,technicien_id=:technicien_id,projet_id=:projet_id,
            date_intervention=:date_intervention,IDPhase=:IDPhase,modifie_par=:modifie_par,Lieux_ouvrage=:Lieux_ouvrage,date_modification=SYSDATE
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':modifie_par', $modifie_par);
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->bindParam(':projet_id', $projet_id);
            $stm->bindParam(':date_intervention', $date_intervention);
            $stm->bindParam(':IDPhase', $IDPhase);
            $stm->bindParam(':Lieux_ouvrage', $Lieux_ouvrage);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function validate($intervention_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET status=2,
            date_modification=NOW()
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function annuler($intervention_id, $obs)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET status=0,obs=:obs,
            date_modification=SYSDATE
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':obs', $obs);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getDemandesInterventions($fromDate, $toDate, $IDAgence)
    {
        try {
            if ($IDAgence == 4) {
                $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.IDClient,Client.abr_client,Phase.libelle,Agence.libelle AS agence_name
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            INNER JOIN Agence ON Agence.IDAgence=Personnel.IDAgence
            WHERE etat_confirmation=0
            AND status=1
            AND date_intervention BETWEEN " . $fromDate . " AND " . $toDate . " 
            ORDER BY interventions.date_intervention DESC");
            } else {
                $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.IDClient,Client.abr_client,Phase.libelle
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE etat_confirmation=0
            AND Personnel.IDAgence=$IDAgence
            AND status=1
            AND date_intervention BETWEEN " . $fromDate . " AND " . $toDate . " 
            ORDER BY interventions.date_intervention DESC");
            }

            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function rejectDemandeIntervention($intervention_id, $obs, $user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET status=0,obs=:obs,
            date_modification=SYSDATE,
            modifie_par=:modifie_par
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':obs', $obs);
            $stm->bindParam(':modifie_par', $user_id);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getDemandesInterventionsTec($user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE etat_confirmation=0
            AND status=1
            AND technicien_id=:technicien_id
            AND cree_par=:technicien_id 
            ORDER BY interventions.date_intervention DESC");
            $stm->bindParam(':technicien_id', $user_id);
            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getNotDoneInterventions($user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Projet.IDProjet,Projet.X,Projet.Y,Client.abr_client,Phase.libelle
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.IDClient=Client.IDClient
            INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
            WHERE etat_confirmation=1
            AND status=1
            AND technicien_id=:technicien_id 
            ORDER BY interventions.date_intervention DESC");
            $stm->bindParam(':technicien_id', $user_id);
            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function updateState($intervention_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET status=2,
            date_modification=SYSDATE
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function interventionsDoneWithoutPV($user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare(
                "SELECT interventions.intervention_id
            FROM interventions
            LEFT JOIN PV ON interventions.intervention_id = PV.intervention_id
            WHERE interventions.status = 2
            AND interventions.technicien_id = :technicien_id
            AND PV.intervention_id IS NULL 
            ORDER BY interventions.date_intervention DESC"
            );
            $stm->bindParam(':technicien_id', $user_id);
            $stm->execute();
            $res = $stm->fetchAll();
            $res = Database::encode_utf8($res);
            return $res;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function confirmeReceptionState($intervention_id, $user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET etat_reception=1,
            date_modification=SYSDATE,
            modifie_par=:user_id
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(":user_id", $user_id);
            $stm->execute();
            return $stm->rowCount();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
