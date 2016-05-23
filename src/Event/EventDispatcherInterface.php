<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/05/16
 * Time: 22:19
 */
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
    public function register($eventCode, callable $listener);

    /**
     * Invoke all event listeners for the given event code
     *
     * @param string         $eventCode
     * @param EventInterface $definition
     * @return $this
     */
    public function dispatch($eventCode, EventInterface $definition);
}
