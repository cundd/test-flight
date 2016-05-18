<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/05/16
 * Time: 22:19
 */
namespace Cundd\TestFlight\Event;

use Cundd\TestFlight\Definition\DefinitionInterface;


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
     * @param string              $eventCode
     * @param DefinitionInterface $definition
     * @return $this
     */
    public function dispatch(string $eventCode, DefinitionInterface $definition);
}
