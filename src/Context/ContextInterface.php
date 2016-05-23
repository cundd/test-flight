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
    public function getVariables();

    /**
     * Store the variable with the given key
     *
     * @param string $key
     * @param mixed  $value
     * @return ContextInterface
     */
    public function setVariable( $key, $value);

    /**
     * Deletes the variable with the given key
     *
     * @param string $key
     * @return ContextInterface
     */
    public function unsetVariable( $key);

    /**
     * Set all variables from the given dictionary
     *
     * @param array $variables
     * @return ContextInterface
     */
    public function addVariables(array $variables);
}
