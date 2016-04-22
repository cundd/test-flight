<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20/04/16
 * Time: 20:26
 */

/**
 * Test if the assertion is true
 *
 * @param mixed  $assertion
 * @param string $message
 */
function test_flight_assert($assertion, string $message = '')
{
    \Cundd\TestFlight\Assert::assert($assertion, $message);
}

/**
 * Test if the callback throws an exception
 *
 * @param callable $callback
 * @param string   $expectedException
 * @param string   $message
 */
function test_flight_throws(callable $callback, string $expectedException = '', string $message = '')
{
    \Cundd\TestFlight\Assert::throws($callback, $expectedException, $message);
}