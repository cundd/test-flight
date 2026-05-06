<?php

use Cundd\TestFlight\Event\EventDispatcherInterface;

// Define the variables '$aVariableSetInBootstrap' and '$eventDispatcher' for
// the tests in src/Context/README.md
return static fn (EventDispatcherInterface $eventDispatcher) => $eventDispatcher->register(
    Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN,
    fn (Cundd\TestFlight\Event\Event $event) => $event->context->addVariables(
        [
            'aVariableSetInBootstrap' => 'variable-was-set-in-bootstrap',
            'eventDispatcher'         => $eventDispatcher,
        ]
    )
);
