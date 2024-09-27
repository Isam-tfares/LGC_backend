<?php
require_once('DB.php');
class BetonTypes
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM Type_beton");
            $stmt->execute();
            $betonTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$betonTypes) {
                return [];
            }
            $betonTypes = Database::encode_utf8($betonTypes);
            return $betonTypes;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
