<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/05/16
 * Time: 21:21
 */

namespace Cundd\TestFlight\Context;

/**
 * Implementation of the test context
 */
class Context implements ContextInterface
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * Returns the variables defined for this context
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx->setVariable('a', 'b');
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(isset($variables['a']));
     *  test_flight_assert_same('b', $variables['a']);
     * </code>
     *
     * @return array
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
     *
     * @param string $key
     * @param mixed  $value
     * @return ContextInterface
     */
    public function setVariable(string $key, $value): ContextInterface
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Deletes the variable with the given key
     *
     * <code>
     *  $ctx = new \Cundd\TestFlight\Context\Context();
     *  $ctx->setVariable('a', 'b');
     *
     *  $ctx->unsetVariable('a');
     *  $variables = $ctx->getVariables();
     *  test_flight_assert(!isset($variables['a']));
     * </code>
     * @param string $key
     * @return ContextInterface
     */
    public function unsetVariable(string $key): ContextInterface
    {
        unset($this->variables[$key]);

        return $this;
    }
}
