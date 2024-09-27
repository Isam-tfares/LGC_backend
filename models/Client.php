<?php
require_once('DB.php');
class Client
{
    public static function getAll()
    {
        try {
            $stm = Database::getInstance()->getConnection()->prepare("SELECT IDClient,abr_client FROM Client Order by abr_client");
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
