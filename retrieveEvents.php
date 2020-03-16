<?php declare(strict_types=1);
include 'scraper.php';
include 'eventSaver.php';
include 'dbEventRetriever.php';
include 'dbConnection.php';

interface EventRetrievalInterface
{
    public function retrieveEvents() : array;
}

class LiveEventRetriever implements EventRetrievalInterface
{
    private function getWebCrawler()
    {
        return new WebCrawler();
    }

    public function retrieveEvents(): array
    {
        $client = $this->getWebCrawler();
        $events = array_filter($client->fetchEvents());
        return $events;
    }
}

class DBEventRetriever implements EventRetrievalInterface
{
    private function getPDO()
    {
        $dbConnection = new PDOConnection();
        return $dbConnection->pdo;
    }

    public function retrieveEvents(): array
    {
        // get events from database
        $pdoConnection = $this->getPDO();
        //use new class to retrieve events
        $dbEventRetriever = new DatabaseEventRetriever($pdoConnection);
        $dbEvents = $dbEventRetriever->getEventsFromDatabase();
        if (!$dbEvents) {
            throw new \Exception('Events could not be retrieved');
        }
        return $dbEvents;
    }
}

class returnEvents
{
    private $retriever;

    /**
     * returnEvents constructor.
     * @param EventRetrievalInterface $retriever
     */
    public function __construct(EventRetrievalInterface $retriever)
    {
        $this->retriever = $retriever;
    }

    public function returnEvents() : void
    {
        // You can guarantee $retriever will have method retrieveEvents
        // as it is in the interface. Everything else is a mystery
        $events = $this->retriever->retrieveEvents();
        // do stuff with events
        echo json_encode($events);
    }
}

class saveEvents
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

$retrieverType = $_POST['callType'] ?? [];

if (empty($retrieverType)) {
    throw new \Exception('Method was called without any data!');
}

if ('dbEvents' === $retrieverType) {
    $retriever = new DBEventRetriever();

    $returner = new returnEvents($retriever);
    $returner->returnEvents();
} else if ('liveEvents') {
    $retriever = new LiveEventRetriever();

    $saver = new saveEvents($retriever);
    $saver->saveEvents();

    $retriever = new DBEventRetriever();

    $returner = new returnEvents($retriever);
    $returner->returnEvents();
} else {
    throw new \Exception('Method was called with an incorrect call type!');
}

