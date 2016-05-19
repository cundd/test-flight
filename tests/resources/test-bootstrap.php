<?php

// Define the variable '$aVariableSetInBootstrap' for the test in src/Context/README.md
/** @var \Cundd\TestFlight\Event\EventDispatcherInterface $eventDispatcher */
$eventDispatcher->register(
    \Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN,
    function (\Cundd\TestFlight\Event\Event $event) {
        $event->getContext()->setVariable('aVariableSetInBootstrap', 'variable-was-set-in-bootstrap');
    }
);
