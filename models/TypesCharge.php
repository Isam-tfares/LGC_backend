<?php
require_once('DB.php');
class TypesCharge
{
    public static function get()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM types_charges");
            $stmt->execute();
            $typesCharges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $typesCharges = Database::encode_utf8($typesCharges);
            if (!$typesCharges) {
                return [];
            }
            return $typesCharges;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
