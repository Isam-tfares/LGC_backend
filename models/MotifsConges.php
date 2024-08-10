<?php
require_once('DB.php');
class MotifsConges
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM motifsconge");
            $stmt->execute();
            $motifsConges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$motifsConges) {
                return -1;
            }
            return $motifsConges;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
