<?php

class Database
{

    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $hf_hostname = $_ENV['HF_HOSTNAME'];
        $hf_port = $_ENV['HF_PORT'];
        $hf_database = $_ENV['HF_DATABASE'];
        $hf_user = $_ENV['HF_USER'];
        $hf_password = $_ENV['HF_PASSWORD'];

        try {
            $hf_dsn = sprintf(
                "odbc:DRIVER={HFSQL};Server Name=%s;Server Port=%s;Database=%s;UID=%s;PWD=%s;",
                $hf_hostname,
                $hf_port,
                $hf_database,
                $hf_user,
                $hf_password
            );
            $this->connection = new PDO($hf_dsn, $hf_user, $hf_password, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
