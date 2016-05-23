<?php

// Define the variables '$aVariableSetInBootstrap' and '$eventDispatcher' for the tests in src/Context/README.md
/** @var \Cundd\TestFlight\Event\EventDispatcherInterface $eventDispatcher */
$eventDispatcher->register(
    \Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN,
    function (\Cundd\TestFlight\Event\Event $event) use ($eventDispatcher) {
        $event->getContext()->addVariables(
            [
                'aVariableSetInBootstrap' => 'variable-was-set-in-bootstrap',
                'eventDispatcher'         => $eventDispatcher,
            ]
        );
    }
);
