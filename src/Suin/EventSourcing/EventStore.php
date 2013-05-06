<?php

namespace Suin\EventSourcing;

interface EventStore
{
    /**
     * @param EventStreamId $startingIdentity
     * @param DomainEvent[] $events
     * @return void
     */
    public function appendWith(EventStreamId $startingIdentity, array $events);

    /**
     * @return void
     */
    public function close();

    /**
     * @param int $lastReceivedEvent
     * @return DispatchableDomainEvent
     */
    public function eventsSince($lastReceivedEvent);

    /**
     * @param EventStreamId $identity
     * @return EventStream
     */
    public function eventStreamSince(EventStreamId $identity);

    /**
     * @param EventStreamId $identity
     * @return EventStream
     */
    public function fullEventStreamFor(EventStreamId $identity);

    /**
     * @return void
     */
    public function purge(); // mainly used for testing

    /**
     * @param EventNotifiable $eventNotifiable
     * @return void
     */
    public function registerEventNotifiable(EventNotifiable $eventNotifiable);
}
