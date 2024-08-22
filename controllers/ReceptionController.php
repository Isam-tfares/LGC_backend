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
        $preReception = Phase_projetController::getPreReceptionByIntervention();
        http_response_code(200);
        echo json_encode($preReception);
    }
    public static function Reception()
    {
        $reception = Phase_projetController::getReceptionByIntervention();
        http_response_code(200);
        echo json_encode($reception);
    }
    public static function ReceptionsRec()
    {
        $receptions = Phase_projetController::ReceptionsChef();
        http_response_code(200);
        echo json_encode($receptions);
    }
}
