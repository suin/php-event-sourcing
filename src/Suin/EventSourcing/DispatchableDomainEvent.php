<?php

namespace Suin\EventSourcing;

class DispatchableDomainEvent
{
    /**
     * @var DomainEvent
     */
    private $domainEvent;

    /**
     * @var int
     */
    private $eventId;

    /**
     * @param int         $eventId
     * @param DomainEvent $domainEvent
     */
    public function __construct($eventId, DomainEvent $domainEvent)
    {
        $this->eventId = $eventId;
        $this->domainEvent = $domainEvent;
    }

    /**
     * @return DomainEvent
     */
    public function domainEvent()
    {
        return $this->domainEvent;
    }

    /**
     * @return int
     */
    public function eventId()
    {
        return $this->eventId;
    }
}
