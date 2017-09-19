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
    /**
     * @var DefinitionInterface
     */
    private $definition;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * Event constructor.
     *
     * @param DefinitionInterface $definition
     * @param ContextInterface    $context
     */
    public function __construct(DefinitionInterface $definition, ContextInterface $context)
    {
        $this->definition = $definition;
        $this->context = $context;
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
