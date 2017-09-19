<?php
declare(strict_types=1);


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

/**
 * Test if the actual value matches the expected
 *
 * @param mixed  $expected
 * @param mixed  $actual
 * @param string $message
 * @throws AssertionError if the values are not equal
 */
function test_flight_assert_same($expected, $actual, string $message = '')
{
    \Cundd\TestFlight\Assert::assertSame($expected, $actual, $message);
}

/**
 * Test if the value is truthy
 *
 * @param mixed  $actual
 * @param string $message
 * @throws AssertionError if the value is false, null, 0, '', '0', ...
 */
function test_flight_assert_true($actual, string $message = '')
{
    \Cundd\TestFlight\Assert::assertTrue($actual, $message);
}

/**
 * Test if the value is falsy
 *
 * @param mixed  $actual
 * @param string $message
 * @throws AssertionError if the value is not false, null, 0, '', '0', ...
 */
function test_flight_assert_false($actual, string $message = '')
{
    \Cundd\TestFlight\Assert::assertFalse($actual, $message);
}

/**
 * Test if the given object is an instance of the given class
 *
 * @param string $className
 * @param mixed  $actual
 * @param string $message
 * @throws \Cundd\TestFlight\Exception\AssertionError
 */
function test_flight_assert_instance_of(string $className, $actual, string $message = '')
{
    \Cundd\TestFlight\Assert::assertInstanceOf($className, $actual, $message);
}

/**
 * Test if the given value is an instance of the given type
 *
 * @param string $type
 * @param mixed  $actual
 * @param string $message
 * @throws \Cundd\TestFlight\Exception\AssertionError
 */
function test_flight_assert_type(string $type, $actual, string $message = '')
{
    \Cundd\TestFlight\Assert::assertTypeOf($type, $actual, $message);
}