<?php

namespace Suin\EventSourcing;

class DefaultEventStream implements EventStream
{
    /**
     * @var DomainEvent[]
     */
    private $events;

    /**
     * @var int
     */
    private $version;

    /**
     * @param DomainEvent[] $eventsList
     * @param int           $version
     */
    public function __construct(array $eventsList, $version)
    {
        $this->setEvents($eventsList);
        $this->setVersion($version);
    }

    /**
     * @return DomainEvent[]
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * @return int
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @param DomainEvent[] $eventsList
     */
    private function setEvents(array $eventsList)
    {
        $this->events = $eventsList;
    }

    /**
     * @param int $version
     */
    private function setVersion($version)
    {
        $this->version = $version;
    }
}
