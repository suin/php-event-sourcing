<?php

namespace Suin\EventSourcing;

interface EventNotifiable
{
    public function notifyDispatchableEvents();
}
