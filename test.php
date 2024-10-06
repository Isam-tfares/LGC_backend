<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');
require("./models/DB.php");


$stm = Database::getInstance()->getConnection()->prepare("SELECT * FROM interventions ORDER BY date_intervention DESC LIMIT 5 ");
$stm->execute();
$receptions = $stm->fetchAll();
$receptions = Database::encode_utf8($receptions);
echo '<h1>Interventions</h1>';
print_r($receptions);
