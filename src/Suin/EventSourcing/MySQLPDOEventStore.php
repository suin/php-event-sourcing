<?php

namespace Suin\EventSourcing;

use Exception;
use PDO;
use PDOStatement;
use RuntimeException;

class MySQLPDOEventStore implements EventStore
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->connection = $pdo;
    }

    /**
     * @param EventStreamId $startingIdentity
     * @param DomainEvent[] $events
     * @throws RuntimeException
     * @return void
     */
    public function appendWith(EventStreamId $startingIdentity, array $events)
    {
        try {
            $index = 0;

            $this->connection->beginTransaction();
            $statement = $this->connection->prepare(
                "INSERT INTO tbl_es_event_store "
                    ."VALUES(:event_id, :event_body, :event_type, :stream_name, :stream_version)"
            );

            foreach ($events as $domainEvent) {
                $statement->bindValue(':event_id', 0, PDO::PARAM_INT);
                $statement->bindValue(':event_body', serialize($domainEvent), PDO::PARAM_STR);
                $statement->bindValue(':event_type', get_class($domainEvent), PDO::PARAM_STR);
                $statement->bindValue(':stream_name', $startingIdentity->streamName(), PDO::PARAM_STR);
                $statement->bindValue(':stream_version', $startingIdentity->streamVersion() + $index, PDO::PARAM_INT);
                $statement->execute();
                $index += 1;
            }

            $this->connection->commit();
            // todo: notify dispatchable events
        } catch (Exception $e) {
            $this->connection->rollBack();

            throw new RuntimeException(
                "Could not append to event store because: ".$e->getMessage(),
                null,
                $e
            );
        }
    }

    /**
     * @return void
     */
    public function close()
    {
        // no-op
    }

    /**
     * @param int $lastReceivedEvent
     * @throws RuntimeException
     * @return DispatchableDomainEvent
     */
    public function eventsSince($lastReceivedEvent)
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT event_id, event_body, event_type FROM tbl_es_event_store WHERE event_id > ? ORDER BY event_id"
            );
            $statement->bindValue(1, $lastReceivedEvent);
            $statement->execute();

            return $this->buildEventSequence($statement);
        } catch (Exception $e) {
            throw new RuntimeException(null, null, $e);
        }
    }

    /**
     * @param EventStreamId $identity
     * @throws RuntimeException
     * @return EventStream
     */
    public function eventStreamSince(EventStreamId $identity)
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT stream_version, event_type, event_body FROM tbl_es_event_store "
                    ."WHERE stream_name = ? AND stream_version >= ? "
                    ."ORDER BY stream_version"
            );

            $statement->bindValue(1, $identity->streamName());
            $statement->bindValue(2, $identity->streamVersion());
            $statement->execute();

            $eventStream = $this->buildEventStream($statement);

            if ($eventStream->version() == 0) {
                throw new RuntimeException(
                    "There is no such event stream: "
                        .$identity->streamName()
                        ." : "
                        .$identity->streamVersion()
                );
            }

            return $eventStream;
        } catch (Exception $e) {
            throw new RuntimeException(null, null, $e);
        }
    }

    /**
     * @param EventStreamId $identity
     * @return EventStream
     */
    public function fullEventStreamFor(EventStreamId $identity)
    {
        // TODO: Implement fullEventStreamFor() method.
    }

    /**
     * @return void
     */
    public function purge()
    {
        // TODO: Implement purge() method.
    }

    /**
     * @param EventNotifiable $eventNotifiable
     * @return void
     */
    public function registerEventNotifiable(EventNotifiable $eventNotifiable)
    {
        // TODO: Implement registerEventNotifiable() method.
    }

    /**
     * @param PDOStatement $results
     * @return DispatchableDomainEvent[]
     */
    private function buildEventSequence(PDOStatement $results)
    {
        $events = [];

        foreach ($results as $result) {
            $eventId = $result['event_id'];
//            $eventClassName = $result['event_type'];
            $eventBody = $result['event_body'];
            $domainEvent = unserialize($eventBody);
            $events[] = new DispatchableDomainEvent($eventId, $domainEvent);
        }

        return $events;
    }

    /**
     * @param PDOStatement $results
     * @return DefaultEventStream
     */
    private function buildEventStream(PDOStatement $results)
    {
        $events = [];
        $version = 0;

        foreach ($results as $result) {
            $version = $result['stream_version'];
//            $eventClassName = $result['event_type'];
            $eventBody = $result['event_body'];
            $domainEvent = unserialize($eventBody);
            $events[] = $domainEvent;
        }

        return new DefaultEventStream($events, $version);
    }
}