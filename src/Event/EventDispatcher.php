<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Event;

use Cundd\TestFlight\Event\Exception\InvalidEventCodeException;

/**
 * Dispatcher for builtin events
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string, callable[]>
     */
    private array $eventListeners = [];

    /**
     * Register a listener for the given event code
     *
     * @return $this
     */
    public function register(
        string $eventCode,
        callable $listener,
    ): EventDispatcherInterface {
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
     * @return $this
     */
    public function dispatch(
        string $eventCode,
        EventInterface $event,
    ): EventDispatcherInterface {
        $this->assertValidEventCode($eventCode);
        if (true === isset($this->eventListeners[$eventCode])) {
            foreach ($this->eventListeners[$eventCode] as $listener) {
                $listener($event);
            }
        }

        return $this;
    }

    private function assertValidEventCode(string $eventCode): void
    {
        if (!ctype_alnum(str_replace(['.', '_'], '', $eventCode))) {
            throw new InvalidEventCodeException(sprintf('Invalid event code "%s"', $eventCode));
        }
    }
}
