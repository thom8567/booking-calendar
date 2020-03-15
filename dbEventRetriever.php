<?php declare(strict_types=1);
class DatabaseEventRetriever
{
    private $pdo;

    /**
     * DatabaseEventRetriever constructor.
     * @param $pdoConnection
     */
    public function __construct($pdoConnection)
    {
        $this->pdo = $pdoConnection;
    }

    public function getEventsFromDatabase() : array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM calendarEvents");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
