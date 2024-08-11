<?php
require_once('DB.php');
class User
{
    static public function userConn($username)
    {
        try {
            // $stm = Database::getInstance()->getConnection()->prepare('SELECT * FROM users WHERE username=:username and password_user=:psd');
            $stm = Database::getInstance()->getConnection()->prepare('SELECT * FROM users WHERE username=:username');
            $stm->bindParam(':username', $username);
            // $stm->bindParam(':psd', $data['user_password']);
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
    static public function getTechniciens()
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM users WHERE user_type='technicien'");
            $stm->execute();
            if ($stm->rowCount()) {
                $res = $stm->fetchAll();
                $res = Database::encode_utf8($res);
                return $res;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
