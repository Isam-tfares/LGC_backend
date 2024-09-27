<?php
require_once('DB.php');
class MotifsConges
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM Nature_conge");
            $stmt->execute();
            $motifsConges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $motifsConges = Database::encode_utf8($motifsConges);
            if (!$motifsConges) {
                return [];
            }
            return $motifsConges;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
