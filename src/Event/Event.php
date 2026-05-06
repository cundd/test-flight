<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Event;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;

/**
 * Event
 */
final readonly class Event implements EventInterface
{
    public function __construct(
        public readonly DefinitionInterface $definition,
        public readonly ContextInterface $context,
    ) {
    }

    /**
     * @deprecated use `definition` directly
     */
    public function getDefinition(): DefinitionInterface
    {
        return $this->definition;
    }

    /**
     * @deprecated use `context` directly
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }
}
