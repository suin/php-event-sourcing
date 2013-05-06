<?php

namespace Suin\EventSourcing;

final class EventStreamId
{
    /**
     * @var string
     */
    private $streamName;

    /**
     * @var int
     */
    private $streamVersion;

    /**
     * @param string $streamName
     * @param int    $streamVersion
     */
    public function __construct($streamName, $streamVersion = 1)
    {
        $this->setStreamName($streamName);
        $this->setStreamVersion($streamVersion);
    }

    /**
     * @return string
     */
    public function streamName()
    {
        return $this->streamName;
    }

    /**
     * @return int
     */
    public function streamVersion()
    {
        return $this->streamVersion;
    }

    /**
     * @param string $streamName
     */
    private function setStreamName($streamName)
    {
        $this->streamName = $streamName;
    }

    /**
     * @param int $streamVersion
     */
    private function setStreamVersion($streamVersion)
    {
        $this->streamVersion = $streamVersion;
    }
}
