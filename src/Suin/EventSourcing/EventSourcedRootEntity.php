<?php

namespace Suin\EventSourcing;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

trait EventSourcedRootEntity
{
    /**
     * @var DomainEvent[]
     */
    private $mutatingEvents = [];

    /**
     * @var int
     */
    private $unmutatedVersion;

    /**
     * @param DomainEvent[] $domainEvents
     * @param int           $streamVersion
     * @return $this
     */
    public static function constructWithEventStream(array $domainEvents, $streamVersion)
    {
        $self = (new ReflectionClass(get_called_class()))->newInstanceWithoutConstructor();

        foreach ($domainEvents as $domainEvent) {
            $self->mutateWhen($domainEvent);
        }

        $self->setUnmutatedVersion($streamVersion);

        return $self;
    }

    /**
     * @return int
     */
    public function mutatedVersion()
    {
        return $this->unmutatedVersion + 1;
    }

    /**
     * @return DomainEvent[]
     */
    public function mutatingEvents()
    {
        return $this->mutatingEvents;
    }

    /**
     * @return int
     */
    public function unmutatedVersion()
    {
        return $this->unmutatedVersion;
    }

    /**
     * @param DomainEvent $event
     */
    private function apply(DomainEvent $event)
    {
        $this->mutatingEvents[] = $event;
        $this->mutateWhen($event);
    }

    /**
     * @param DomainEvent $domainEvent
     * @throws RuntimeException
     */
    private function mutateWhen(DomainEvent $domainEvent)
    {
        $eventType = (new ReflectionClass($domainEvent))->getShortName();
        $mutatorMethodName = 'when'.$eventType;

        if (method_exists($this, $mutatorMethodName) === false) {
            throw new RuntimeException(
                sprintf("Method %s() does not exist", $mutatorMethodName)
            );
        }

        try {
            $mutatorMethod = new ReflectionMethod($this, $mutatorMethodName);
            $mutatorMethod->setAccessible(true);
            $mutatorMethod->invoke($this, $domainEvent);
        } catch (ReflectionException $e) {
            throw new RuntimeException(
                sprintf("Method %s() failed", $mutatorMethodName), null, $e
            );
        }
    }

    /**
     * @param int $streamVersion
     */
    private function setUnmutatedVersion($streamVersion)
    {
        $this->unmutatedVersion = $streamVersion;
    }
}
