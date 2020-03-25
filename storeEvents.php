<?php declare(strict_types=1);
class StoreEvents
{
    private $retriever;

    /**
     * saveEvents constructor.
     * @param EventRetrievalInterface $retriever
     */
    public function __construct(EventRetrievalInterface $retriever)
    {
        $this->retriever = $retriever;
    }

    public function saveEvents() : void
    {
        $pdo = $this->getPDO();
        $events = $this->retriever->retrieveEvents();
        $eventSaver = new EventSaver($pdo, $events);
        $eventSaver->saveEvents();
    }

    private function getPDO()
    {
        $dbConnection = new PDOConnection();
        return $dbConnection->pdo;
    }
}

