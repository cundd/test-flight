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
     * @var array<non-empty-string, callable[]>
     */
    private array $eventListeners = [];

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

    /**
     * @phpstan-assert non-empty-string $eventCode
     */
    private function assertValidEventCode(string $eventCode): void
    {
        if (!ctype_alnum(str_replace(['.', '_'], '', $eventCode))) {
            throw new InvalidEventCodeException(sprintf(
                'Invalid event code "%s"',
                $eventCode
            ));
        }
    }
}
