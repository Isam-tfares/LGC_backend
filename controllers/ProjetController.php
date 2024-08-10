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
        if ($projets == -1) {
            http_response_code(404);
            return json_encode(["message" => "No projets found."]);
        }
        http_response_code(200);
        return json_encode($projets);
    }
}
