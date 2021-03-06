<?php
declare(strict_types=1);


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

    /**
     * Set all variables from the given dictionary
     *
     * @param array $variables
     * @return ContextInterface
     */
    public function addVariables(array $variables): ContextInterface;
}
