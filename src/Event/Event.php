<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Event;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;

/**
 * Event
 */
class Event implements EventInterface
{
    public function __construct(
        private readonly DefinitionInterface $definition,
        private readonly ContextInterface $context,
    ) {
    }

    /**
     * @return DefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
