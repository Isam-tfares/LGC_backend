<?php
require_once('DB.php');
class User
{
    static public function userConn($matricule)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare('SELECT * FROM Personnel WHERE mle_personnel=:matricule');
            $stm->bindParam(':matricule', $matricule);
            // $stm->bindParam(':psd', $data['user_password']);
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetch();
                return $res;
            } else {
                return [];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    static public function getTechniciens($IDAgence)
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM Personnel  WHERE Personnel.IDFonction_personnel=3 AND Personnel.IDAgence=$IDAgence");
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
}
