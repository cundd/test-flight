<?php

declare(strict_types=1);

/**
 * Test if the assertion is true
 */
function test_flight_assert(mixed $assertion, string $message = ''): void
{
    Cundd\TestFlight\Assert::assert($assertion, $message);
}

/**
 * Test if the callback throws an exception
 */
function test_flight_throws(
    callable $callback,
    string $expectedException = '',
    string $message = '',
): void {
    Cundd\TestFlight\Assert::throws($callback, $expectedException, $message);
}

/**
 * Test if the actual value matches the expected
 *
 * @template T
 *
 * @param T $expected
 * @param T $actual
 *
 * @throws AssertionError if the values are not equal
 */
function test_flight_assert_same(
    mixed $expected,
    mixed $actual,
    string $message = '',
): void {
    Cundd\TestFlight\Assert::assertSame($expected, $actual, $message);
}

/**
 * Test if the value is truthy
 *
 * @throws AssertionError if the value is false, null, 0, '', '0', ...
 */
function test_flight_assert_true(
    mixed $actual,
    string $message = '',
): void {
    Cundd\TestFlight\Assert::assertTrue($actual, $message);
}

/**
 * Test if the value is falsy
 *
 * @throws AssertionError if the value is not false, null, 0, '', '0', ...
 */
function test_flight_assert_false(
    mixed $actual,
    string $message = '',
): void {
    Cundd\TestFlight\Assert::assertFalse($actual, $message);
}

/**
 * Test if the given object is an instance of the given class
 *
 * @template T
 *
 * @param class-string<T> $className
 * @param T               $actual
 *
 * @throws Cundd\TestFlight\Exception\AssertionError
 */
function test_flight_assert_instance_of(
    string $className,
    mixed $actual,
    string $message = '',
): void {
    Cundd\TestFlight\Assert::assertInstanceOf($className, $actual, $message);
}

/**
 * Test if the given value is an instance of the given type
 *
 * @throws Cundd\TestFlight\Exception\AssertionError
 */
function test_flight_assert_type(
    string $type,
    mixed $actual,
    string $message = '',
): void {
    Cundd\TestFlight\Assert::assertTypeOf($type, $actual, $message);
}
