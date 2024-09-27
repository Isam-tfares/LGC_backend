<?php
require_once('DB.php');
class EchantillonNatures
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM Nature_echantillon");
            $stmt->execute();
            $echantillonNatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $echantillonNatures = Database::encode_utf8($echantillonNatures);
            if (!$echantillonNatures) {
                return [];
            }
            return $echantillonNatures;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
