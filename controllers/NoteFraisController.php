<?php
class NoteFraisController
{
    public static function getDemandes($user_id)
    {
        $demandes = NoteFrais::getDemandes($user_id);
        return $demandes;
    }
    public static function insertNote($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["message" => "Please provide all required fields"]);
            return;
        }

        $response = NoteFrais::addNote($data, $user_id);
        return $response;
    }
}
