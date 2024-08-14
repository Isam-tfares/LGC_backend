<?php
class ReceptionController
{
    public static function PVs()
    {
        $pvs = PVController::getPVs();
        http_response_code(200);
        echo json_encode($pvs);
    }
    public static function PrereceptionsRec()
    {
        $prereceptions = Phase_projetController::PreReceptionsChef();
        http_response_code(200);
        echo json_encode($prereceptions);
    }
    public static function PreReception()
    {
        $preReception = Phase_projetController::getPreReception();
        http_response_code(200);
        echo json_encode($preReception);
    }
    public static function Reception()
    {
        $reception = Phase_projetController::getReception();
        http_response_code(200);
        echo json_encode($reception);
    }
}
