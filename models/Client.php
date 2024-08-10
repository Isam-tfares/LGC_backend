<?php
require_once('DB.php');
class Client
{
    public static function getAll()
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM Client");
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
}
