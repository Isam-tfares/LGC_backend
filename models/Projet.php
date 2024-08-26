<?php
require_once('DB.php');
class Projet
{
    public static function getAll()
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT IDProjet,abr_projet,IDClient FROM Projet Order by abr_projet");
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                $res = Database::encode_utf8($res);
                return $res;
            } else {
                return [];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getAllClient($client_id)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM Projet WHERE IDClient = :client_id Order by abr_projet");
            $stm->bindParam(':client_id', $client_id);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                $res = Database::encode_utf8($res);
                return $res;
            } else {
                return [];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function addLocation($user_id, $X, $Y, $IDProjet)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("UPDATE Projet SET X=:X,Y=:Y,ModifiePar=:user WHERE IDProjet=:id");
            $stm->bindParam(':X', $X);
            $stm->bindParam(':Y', $Y);
            $stm->bindParam(':id', $IDProjet);
            $stm->bindParam(':user', $user_id);
            $stm->execute();
            if ($stm->rowCount()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
