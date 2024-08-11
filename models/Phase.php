<?php
require_once('DB.php');
class Phase
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT IDPhase,libelle FROM Phase");
            $stmt->execute();
            $phases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $phases = Database::encode_utf8($phases);
            if (!$phases) {
                return -1;
            }
            return $phases;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
