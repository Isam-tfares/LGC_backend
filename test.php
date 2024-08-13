<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once('./autoload.php');


$headers = apache_request_headers();
$token = $headers['authorization'];
$token = str_replace('Bearer ', '', $token);
$data = Client::getAll();
$data = Database::encode_utf8($data);
$response = json_encode(["clients" => $data[0], "token" => $token]);
header('Content-Type: application/json');
echo json_encode($response);
