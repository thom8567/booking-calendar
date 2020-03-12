<?php declare(strict_types=1);
require __DIR__ . "/includes/sql_connection.php";
require __DIR__ . "/vendor/autoload.php";
use Goutte\Client as GoutteClient;

class WebCrawler
{
    private $client;
    private $crawler;

    /**
     * WebCrawler constructor.
     */
    public function __construct()
    {
        $this->client = $this->createClient();
        $this->crawler = $this->createCrawler();
    }

    /**
     * @return GoutteClient
     */
    private function createClient() : GoutteClient
    {
        return new GoutteClient();
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function createCrawler() : \Symfony\Component\DomCrawler\Crawler
    {
        return $this->client->request('GET', 'https://www.britishrowing.org/rowing-activity-finder/calendar/');
    }

    /**
     * @return array
     */
    public function fetchEvents() : array
    {
        return $this->crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
            if ($tr->filter('.rich-results__result__content__title')->count() === 0) {
                return [];
            }
            $response = [
                'title' => $tr->filter('.rich-results__result__content__title')->text(),
                'date' => (new DateTime($tr->filter('.rich-results__result__content__date__datetime')->text()))->format('Y-m-d'),
            ];
            $tr->filter('.rich-results__result__content__synopsis')->each(function ($item) use (&$response) {
                $itemText = $item->text();
                if (!strpos($item->text(), ':')) {
                    $response[] = $itemText;
                }
                $itemText = explode(':', $item->text());
                $response[str_replace(' ', '_', mb_strtolower($itemText[0]))] = trim($itemText[1]);
            });
            return $response;
        });
    }
}

class EventSaver
{
    private $pdo;
    private $events;

    /**
     * EventSaver constructor.
     * @param $pdoConnection
     * @param $events
     */
    public function __construct($pdoConnection, array $events)
    {
        $this->pdo = $pdoConnection;
        $this->events = $events;
    }

    public function saveEvents() : void
    {
        foreach($this->events as $key => $value) {

            if (empty($value['title']) || empty($value['date'])) {
                continue;
            }
            $eventName = $value['title'];
            $eventDate = $value['date'];
            $eventDetails = json_encode([
                $value['region'] ?? '',
                $value['category'] ?? '',
                $value['status'] ?? '',
                $value['booking_deadline'] ?? '',
                $value['planned_closing_date'] ?? ''
            ]);

            $selectResults = $this->checkEventExists($eventName);

            if ($selectResults) {
                $this->updateEvent($eventName, $eventDate, $eventDetails);
            } else {
                $this->insertEvent($eventName, $eventDate, $eventDetails);
            }
        }
        echo('success');
    }

    private function checkEventExists(string $eventName) : array
    {
        $selectStmt = $this->pdo->prepare("SELECT * FROM calendarEvents where eventName = ?");
        $selectStmt->execute([$eventName]);
        return $selectStmt->fetchAll();
    }

    private function insertEvent(string $eventName, string $eventDate, string $eventDetails) : void
    {
        $insertStmt = $this->pdo->prepare("INSERT INTO calendarEvents(eventName, eventStartDate, eventDetails) VALUES (?, ?, ?)");
        $insertStmt->execute([$eventName, $eventDate, $eventDetails]);
        if (!$insertStmt) {
            echo('fail');
            die();
        }
        $stmt = null;
    }

    private function updateEvent(string $eventName, string $eventDate, string $eventDetails) : void
    {
        $updateStmt = $this->pdo->prepare("UPDATE calendarEvents SET eventStartDate = ?, eventDetails = ? where eventName = ?");
        $updateStmt->execute([$eventDate, $eventDetails, $eventName]);
        if (!$updateStmt) {
            echo('fail');
            die();
        }
    }
}

$client = new WebCrawler();

$events = array_filter($client->fetchEvents());

$eventSaver = new EventSaver($pdo, $events);

$eventSaver->saveEvents();