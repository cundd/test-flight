<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Context;

/**
 * Implementation of the test context
 */
class Context implements ContextInterface
{
    /**
     * @var array<non-empty-string,mixed>
     */
    private array $variables = [];

    /**
     * Return the variables defined for this context
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx->setVariable('a', 'b');
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(isset($variables['a']));
     *  test_flight_assert_same('b', $variables['a']);
     * </code>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Store the variable with the given key
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx
     *      ->setVariable('a', 'b')
     *      ->setVariable('c', 100);
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(isset($variables['a']));
     *  test_flight_assert_same('b', $variables['a']);
     *  test_flight_assert(isset($variables['c']));
     *  test_flight_assert_same(100, $variables['c']);
     * </code>
     */
    public function setVariable(string $key, mixed $value): ContextInterface
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Delete the variable with the given key
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx->setVariable('a', 'b');
     *
     *  $ctx->unsetVariable('a');
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(!isset($variables['a']));
     * </code>
     */
    public function unsetVariable(string $key): ContextInterface
    {
        unset($this->variables[$key]);

        return $this;
    }

    /**
     * Set all variables from the given dictionary
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx->addVariables([
     *      'a' => 'b',
     *      'c' => 100,
     *  ]);
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(isset($variables['a']), 'Variable "a" not defined');
     *  test_flight_assert_same('b', $variables['a']);
     *  test_flight_assert(isset($variables['c']), 'Variable "c" not defined');
     *  test_flight_assert_same(100, $variables['c']);
     *
     * </code>
     */
    public function addVariables(array $variables): ContextInterface
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }
}
