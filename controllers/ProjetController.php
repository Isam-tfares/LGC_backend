<?php
class ProjetController
{
    static public function getAll()
    {
        $data = json_decode(file_get_contents("php://input"));
        $client_id = $data->client_id ?? '';
        if (empty($client_id)) {
            $projets = Projet::getAll();
        } else {
            $projets = Projet::getAllClient($client_id);
        }
        return $projets;
    }
    static public function addLocation($user_id)
    {
        $data = json_decode(file_get_contents("php://input"));
        $X = $data->X ?? '';
        $Y = $data->Y ?? '';
        $IDProjet = $data->IDProjet ?? '';
        if (empty($X) || empty($Y) || empty($IDProjet)) {
            http_response_code(404);
            return json_encode(["message" => "All champs are required."]);
        }
        $response = Projet::addLocation($user_id, $X, $Y, $IDProjet);
        return $response;
    }
}
