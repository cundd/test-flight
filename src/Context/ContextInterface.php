<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/05/16
 * Time: 21:13
 */

namespace Cundd\TestFlight\Context;

/**
 * Interface for the test context
 */
interface ContextInterface
{
    /**
     * Returns the variables defined for this context
     *
     * @return array
     */
    public function getVariables(): array;

    /**
     * Store the variable with the given key
     *
     * @param string $key
     * @param mixed  $value
     * @return ContextInterface
     */
    public function setVariable(string $key, $value): ContextInterface;

    /**
     * Deletes the variable with the given key
     *
     * @param string $key
     * @return ContextInterface
     */
    public function unsetVariable(string $key): ContextInterface;
}
