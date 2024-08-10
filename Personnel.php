<?php
require 'vendor/autoload.php';
require 'connect.php';
require 'utils.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

$db = Database::getInstance()->getConnection();

$query = $db->prepare("select * from Personnel");
$query->execute();
$personnels = $query->fetchAll(PDO::FETCH_ASSOC);
// echo json_encode($personnels);
print_r($personnels);
