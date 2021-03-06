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
     *
     * @param string   $eventCode
     * @param callable $listener
     * @return $this
     */
    public function register(string $eventCode, callable $listener);

    /**
     * Invoke all event listeners for the given event code
     *
     * @param string         $eventCode
     * @param EventInterface $definition
     * @return $this
     */
    public function dispatch(string $eventCode, EventInterface $definition);
}
