<?php
require_once('DB.php');
class PV
{
    public static function insertPV($intervention_id, $imageName, $IDPre_reception)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("INSERT INTO PV (intervention_id,image_path,date_creation,IDPre_reception)
            VALUES (:intervention_id, :image_path, SYSDATE,:IDPre_reception)");
            $stm->bindParam(':intervention_id', $intervention_id);
            $stm->bindParam(':image_path', $imageName);
            $stm->bindParam(':IDPre_reception', $IDPre_reception);
            $stm->execute();
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getPV($IDPv)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM PV WHERE IDPv=:IDPv");
            $stm->bindParam(":IDPv", $IDPv);
            $stm->execute();
            return $stm->fetch();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getPVs($fromDate, $toDate)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT PV.*,interventions.*,Personnel.Nom_personnel FROM PV
            INNER JOIN interventions ON interventions.intervention_id=PV.intervention_id
            INNER JOIN Personnel ON Personnel.IDPersonnel=interventions.technicien_id
             WHERE PV.date_creation BETWEEN " . $fromDate . " AND " . $toDate);
            $stm->execute();
            return $stm->fetchAll();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getPVsTec($fromDate, $toDate, $user_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM PV WHERE
            intervention_id IN (SELECT intervention_id FROM interventions WHERE technicien_id=" . $user_id . " )
            AND date_creation BETWEEN " . $fromDate . " AND " . $toDate);
            $stm->execute();
            return $stm->fetchAll();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
