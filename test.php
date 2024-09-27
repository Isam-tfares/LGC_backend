<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');
require("./models/DB.php");


$stm = Database::getInstance()->getConnection()->prepare("SELECT interventions.*,Personnel.Nom_personnel,Projet.abr_projet,Projet.Objet_Projet,Client.abr_client,Phase.libelle
                FROM interventions
                INNER JOIN Personnel ON interventions.technicien_id=Personnel.IDPersonnel
                INNER JOIN Projet ON interventions.projet_id=Projet.IDProjet
                INNER JOIN Client ON Projet.IDClient=Client.IDClient 
                INNER JOIN Phase ON interventions.IDPhase=Phase.IDPhase
                WHERE interventions.etat_confirmation=1 
                AND Personnel.IDAgence=:IDAgence
                AND date_intervention Between " . 20201010 . " and " . 20301010 . " 
                ORDER BY interventions.date_intervention DESC");
$stm->bindParam(':IDAgence', $IDAgence);
$stm->execute();
$receptions = $stm->fetchAll();
$receptions = Database::encode_utf8($receptions);
echo '<h1>PreReception</h1>';
print_r($receptions);
