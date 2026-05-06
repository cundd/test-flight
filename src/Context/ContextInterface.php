<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Context;

/**
 * Interface for the test context
 */
interface ContextInterface
{
    /**
     * Return the variables defined for this context
     *
     * @return array<non-empty-string,mixed>
     */
    public function getVariables(): array;

    /**
     * Store the variable with the given key
     *
     * @param non-empty-string $key
     */
    public function setVariable(string $key, mixed $value): ContextInterface;

    /**
     * Delete the variable with the given key
     *
     * @param non-empty-string $key
     */
    public function unsetVariable(string $key): ContextInterface;

    /**
     * Set all variables from the given dictionary
     *
     * @param array<non-empty-string,mixed> $variables
     */
    public function addVariables(array $variables): ContextInterface;
}
