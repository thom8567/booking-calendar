<?php declare(strict_types=1);
include 'scraper.php';
include 'eventSaver.php';
include 'dbEventRetriever.php';
include 'dbConnection.php';
include 'displayEvents.php';
include 'storeEvents.php';

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

$retrieverSource = $_POST['source'] ?? '';

if (empty($retrieverSource)) {
    throw new \Exception('Method was called without any data!');
}

if (!in_array($retrieverSource, ['dbEvents', 'liveEvents'])) {
    throw new \Exception('Method was called with an incorrect call type!');
}

if ('liveEvents' === $retrieverSource) {
    $saver = new StoreEvents(new LiveEventRetriever());
    $saver->saveEvents();
}

$displayer = new DisplayEvents(new DBEventRetriever());
$displayer->returnEvents();

