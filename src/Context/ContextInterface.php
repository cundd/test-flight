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
     */
    public function getVariables(): array;

    /**
     * Store the variable with the given key
     */
    public function setVariable(string $key, $value): ContextInterface;

    /**
     * Deletes the variable with the given key
     */
    public function unsetVariable(string $key): ContextInterface;

    /**
     * Set all variables from the given dictionary
     */
    public function addVariables(array $variables): ContextInterface;
}
