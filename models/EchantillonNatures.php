<?php
require_once('DB.php');
class EchantillonNatures
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM echantillonnatures");
            $stmt->execute();
            $echantillonNatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$echantillonNatures) {
                return -1;
            }
            return $echantillonNatures;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
