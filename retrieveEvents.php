<?php declare(strict_types=1);
include 'scraper.php';
include 'eventSaver.php';

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
    }
}
$retriever = new LiveEventRetriever();
// OR
$retriever = new DBEventRetriever();
class EventDisplayer
{
    private $retriever;
    public function __construct(EventRetrivalInterface $retriever)
    {
        $this->retriever = $retriever;
    }
    public function displayEvents()
    {
        // You can guarantee $retriever will have method retrieveEvents
        // as it is in the interface. Everything else is a mystery
        $events = $this->retriever->retrieveEvents();
        // do stuff with events
    }
}
// Both will work as it uses the interface for type checking
$displayer = new EventDisplayer(new LiveEventRetriever());
$displayer = new EventDisplayer(new DBEventRetriever());
$displayer->displayEvents();



$eventSaver = new EventSaver($pdo, $events);

$eventSaver->saveEvents();

//echo json_encode($events);
