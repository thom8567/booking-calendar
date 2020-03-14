<?php declare(strict_types=1);
class PDOConnection
{
    public $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=derbyRowingClub', 'homestead', 'secret');
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



