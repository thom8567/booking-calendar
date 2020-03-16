<?php declare(strict_types=1);

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
                'region' => $value['region'] ?? '',
                'category' => $value['category'] ?? '',
                'status' => $value['status'] ?? '',
                'booking_deadline' => $value['booking_deadline'] ?? '',
                'planned_closing_date' => $value['planned_closing_date'] ?? ''
            ]);

            $selectResults = $this->checkEventExists($eventName);

            if ($selectResults) {
                $this->updateEvent($eventName, $eventDate, $eventDetails);
            } else {
                $this->insertEvent($eventName, $eventDate, $eventDetails);
            }
        }
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
            echo(json_encode('fail'));
            die();
        }
        $stmt = null;
    }

    private function updateEvent(string $eventName, string $eventDate, string $eventDetails) : void
    {
        $updateStmt = $this->pdo->prepare("UPDATE calendarEvents SET eventStartDate = ?, eventDetails = ? where eventName = ?");
        $updateStmt->execute([$eventDate, $eventDetails, $eventName]);
        if (!$updateStmt) {
            echo(json_encode('fail'));
            die();
        }
        $stmt = null;
    }
}
