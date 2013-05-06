<?php

namespace Suin\EventSourcing;

interface EventStream
{
    /**
     * @return DomainEvent[]
     */
    public function events();

    /**
     * @return int
     */
    public function version();
}
