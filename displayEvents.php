<?php declare(strict_types=1);
class DisplayEvents
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
