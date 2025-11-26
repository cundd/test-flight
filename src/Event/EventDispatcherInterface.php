<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Event;

/**
 * Dispatcher for builtin events
 */
interface EventDispatcherInterface
{
    /**
     * Register a listener for the given event code
     */
    public function register(
        string $eventCode,
        callable $listener,
    ): EventDispatcherInterface;

    /**
     * Invoke all event listeners for the given event code
     */
    public function dispatch(
        string $eventCode,
        EventInterface $definition,
    ): EventDispatcherInterface;
}
