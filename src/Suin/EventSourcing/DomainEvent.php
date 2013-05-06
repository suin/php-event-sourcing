<?php

namespace Suin\EventSourcing;

use DateTime;

interface DomainEvent
{
    /**
     * @return DateTime
     */
    public function occurredOn();
}
