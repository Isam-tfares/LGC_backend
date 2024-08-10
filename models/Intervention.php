<?php
require_once('DB.php');
class Intervention
{
    static public function getAll($fromDate, $toDate)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Client.abr_client
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.client_id=Client.IDClient 
            WHERE etat_confirmation=1");
            // AND date_intervention BETWEEN TO_DATE(:fromDate, 'DD/MM/YYYY') AND TO_DATE(:toDate, 'DD/MM/YYYY')");
            // $stm->bindParam(':fromDate', $fromDate);
            // $stm->bindParam(':toDate', $toDate);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                return $res;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getAllTechnicien($technicien_id, $date)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,
            Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet 
            INNER JOIN Client ON Projet.client_id=Client.IDClient 
            WHERE technicien_id=:id AND date_intervention=TO_DATE(:date_intervention, 'DD/MM/YYYY')
            AND etat_confirmation=1");
            $stm->bindParam(':id', $technicien_id);
            $stm->bindParam(':date_intervention', $date);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                return $res;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function get($intervention_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Client.abr_client
            FROM interventions 
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel 
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet 
            INNER JOIN Client ON Projet.client_id=Client.IDClient 
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetch();
                return $res;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function insert($technicien_id, $projet_id, $date_intervention, $etat, $created_by)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("INSERT INTO interventions 
            (technicien_id,projet_id,date_intervention,etat_confirmation,cree_par,date_creation)
            VALUES (:technicien_id,:projet_id,TO_DATE(:date_intervention, 'DD/MM/YYYY'),:etat_confirmation,:cree_par,NOW())");
            $stm->bindParam(':technicien_id', $technicien_id);
            $stm->bindParam(':projet_id', $projet_id);
            $stm->bindParam(':date_intervention', $date_intervention);
            $stm->bindParam(':etat_confirmation', $etat);
            $stm->bindParam(':cree_par', $created_by);
            $stm->execute();
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
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
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function confirmate($intervention_id, $modifie_par)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET etat_confirmation=1,
            modifie_par=:modifie_par
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':modifie_par', $modifie_par);
            $stm->execute();
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
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
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
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
            date_modification=NOW()
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':obs', $obs);
            $stm->execute();
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getDemandesInterventions($fromDate, $toDate)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Client.abr_client
            FROM interventions
            INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
            INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
            INNER JOIN Client ON Projet.client_id=Client.IDClient
            WHERE date_intervention BETWEEN TO_DATE(:fromDate, 'DD/MM/YYYY') AND TO_DATE(:toDate, 'DD/MM/YYYY')
            AND etat_confirmation=0
            AND status=1");
            $stm->bindParam(':fromDate', $fromDate);
            $stm->bindParam(':toDate', $toDate);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                return $res;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function rejectDemandeIntervention($intervention_id, $obs)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE interventions 
            SET status=0,obs=:obs,
            date_modification=NOW()
            WHERE intervention_id=:intervention_id");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':obs', $obs);
            $stm->execute();
            if ($stm->rowCount()) {
                return 1;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
