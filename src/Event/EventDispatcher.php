<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/05/16
 * Time: 21:18
 */

namespace Cundd\TestFlight\Event;


use Cundd\TestFlight\Event\Exception\InvalidEventCodeException;

/**
 * Dispatcher for builtin events
 */
class EventDispatcher implements EventDispatcherInterface
{
    private $eventListeners = [];

    /**
     * Register a listener for the given event code
     *
     * @param string   $eventCode
     * @param callable $listener
     * @return $this
     */
    public function register(string $eventCode, callable $listener)
    {
        $this->assertValidEventCode($eventCode);
        if (false === isset($this->eventListeners[$eventCode])) {
            $this->eventListeners[$eventCode] = [];
        }
        $this->eventListeners[$eventCode][] = $listener;

        return $this;
    }

    /**
     * Invoke all event listeners for the given event code
     *
     * @param string         $eventCode
     * @param EventInterface $event
     * @return $this
     */
    public function dispatch(string $eventCode, EventInterface $event)
    {
        $this->assertValidEventCode($eventCode);
        if (true === isset($this->eventListeners[$eventCode])) {
            foreach ($this->eventListeners[$eventCode] as $listener) {
                $listener($event);
            }
        }

        return $this;
    }

    /**
     * @param string $eventCode
     */
    private function assertValidEventCode(string $eventCode)
    {
        if (!ctype_alnum(str_replace(['.', '_'], '', $eventCode))) {
            throw new InvalidEventCodeException(sprintf('Invalid event code "%s"', $eventCode));
        }
    }
}


