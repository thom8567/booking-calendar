<?php declare(strict_types=1);
class PDOConnection
{
    public $pdo;

    public function __construct()
    {
        try {
            //Connect to the WP db
            $this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Setting a persistent connection to remove overhead as the details above will not change
            $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
            //Setting default fetch mode to get associative arrays
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            throw new Exception($e->getMessage());
        }
    }
}



