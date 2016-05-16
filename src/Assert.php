<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 22/04/16
 * Time: 22:08
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\AssertionError;
use Cundd\TestFlight\Exception\AssertionLogicError;


/**
 * A collection of assert functions
 */
abstract class Assert
{
    /**
     * @var int
     */
    private static $count = 0;

    /**
     * Test if the assertion is true
     *
     * @example
     *  \Cundd\TestFlight\Assert::assert(true);
     *  test_flight_throws(function() {
     *      \Cundd\TestFlight\Assert::assert(false);
     *  });
     *
     * @param mixed  $assertion
     * @param string $message
     */
    public static function assert($assertion, $message = '')
    {
        self::$count += 1;
        
        if (!$assertion) {
            throw new AssertionError($message);
        }
    }

    /**
     * Test if the actual value matches the expected
     *
     * @example
     *  \Cundd\TestFlight\Assert::assertSame([], []);
     *  \Cundd\TestFlight\Assert::assertSame(true, true);
     *  \Cundd\TestFlight\Assert::assertSame(PHP_INT_MAX, PHP_INT_MAX);
     *  $object = new stdClass(); \Cundd\TestFlight\Assert::assertSame($object, $object);
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertSame(true, false); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertSame([], [1]); });
     *  test_flight_throws(function() use($object) { \Cundd\TestFlight\Assert::assertSame($object, new stdClass()); });
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @throws AssertionError if the values are not equal
     */
    public static function assertSame($expected, $actual, $message = '')
    {
        self::$count += 1;

        if ($expected === $actual) {
            return;
        }
        if (!$message) {
            $message = sprintf(
                'Failed asserting that %s matches %s',
                var_export($actual, true),
                var_export($expected, true)
            );
        }
        throw new AssertionError($message);
    }

    /**
     * Test if the value is truthy
     *
     * @example
     *  \Cundd\TestFlight\Assert::assertTrue(true);
     *  \Cundd\TestFlight\Assert::assertTrue(1);
     *  \Cundd\TestFlight\Assert::assertTrue(' ');
     *  \Cundd\TestFlight\Assert::assertTrue('1');
     *  \Cundd\TestFlight\Assert::assertTrue(new stdClass());
     *  \Cundd\TestFlight\Assert::assertTrue([1]);
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertTrue(false); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertTrue(0); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertTrue(''); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertTrue(null); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertTrue([]); });
     *
     * @param mixed  $actual
     * @param string $message
     * @throws AssertionError if the value is false, null, 0, '', '0', ...
     */
    public static function assertTrue($actual, $message = '')
    {
        self::$count += 1;

        if ((bool)$actual) {
            return;
        }

        if (!$message) {
            $message = sprintf(
                'Failed asserting that %s is truthy',
                var_export($actual, true)
            );
        }
        throw new AssertionError($message);
    }

    /**
     * Test if the value is falsy
     *
     * @example
     *  \Cundd\TestFlight\Assert::assertFalse(false);
     *  \Cundd\TestFlight\Assert::assertFalse(0);
     *  \Cundd\TestFlight\Assert::assertFalse('');
     *  \Cundd\TestFlight\Assert::assertFalse(null);
     *  \Cundd\TestFlight\Assert::assertFalse([]);
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse(true); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse(1); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse(' '); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse('1'); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse(new stdClass()); });
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertFalse([1]); });
     *
     * @param mixed  $actual
     * @param string $message
     * @throws AssertionError if the value is false, null, 0, '', '0', ...
     */
    public static function assertFalse($actual, $message = '')
    {
        self::$count += 1;

        if (false === (bool)$actual) {
            return;
        }

        if (!$message) {
            $message = sprintf(
                'Failed asserting that %s is falsy',
                var_export($actual, true)
            );
        }
        throw new AssertionError($message);
    }

    /**
     * Test if the callback throws an exception
     *
     * @example
     *
     *  test_flight_throws(function() { throw new \Exception(); });
     *  test_flight_throws(function() { throw new \RuntimeException(); }, 'RuntimeException');
     *
     *  $didThrow = false;
     *  try {
     *      test_flight_throws(function() { return true; });
     *  } catch (\Cundd\TestFlight\Exception\AssertionError $e) {
     *      $didThrow = true;
     *  }
     *  assert($didThrow);
     *
     *  $didThrowWrongException = false;
     *  try {
     *      test_flight_throws(function() { throw new \LogicException(); }, 'RuntimeException');
     *  } catch (\Cundd\TestFlight\Exception\AssertionError $e) {
     *      $didThrowWrongException = true;
     *  }
     *  assert($didThrowWrongException);
     *
     * @param callable $callback
     * @param string   $expectedException
     * @param string   $message
     * @throws AssertionError
     */
    public static function throws(callable $callback, $expectedException = '', $message = '')
    {
        self::$count += 1;

        $exception = null;
        try {
            $callback();
        } catch (\Error $exception) {

        } catch (\Exception $exception) {

        }

        if ($exception === null) {
            throw new AssertionError($message ?: 'No exception thrown');
        } elseif ($expectedException && !is_a($exception, $expectedException)) {
            throw new AssertionError(
                $message ?: sprintf(
                    'Expected %s got %s',
                    $expectedException,
                    get_class($exception)
                )
            );
        }
    }

    /**
     * Test if the given object is an instance of the given class
     *
     * @example
     *  \Cundd\TestFlight\Assert::assertInstanceOf(stdClass::class, new stdClass());
     *  \Cundd\TestFlight\Assert::assertInstanceOf('stdClass', new stdClass());
     *  test_flight_throws(function() { \Cundd\TestFlight\Assert::assertInstanceOf('stdClass', new Exception()); });
     *
     * @param string $className
     * @param mixed  $actual
     * @param string $message
     * @throws AssertionError
     */
    public static function assertInstanceOf($className, $actual, $message = '')
    {
        self::$count += 1;

        if ($actual instanceof $className) {
            return;
        }

        if (!$message) {
            $message = sprintf(
                'Failed asserting that %s is an instance of %s',
                static::getType($actual),
                $className
            );
        }
        throw new AssertionError($message);
    }

    /**
     * Test if the given value is an instance of the given type
     *
     * @example
     *  \Cundd\TestFlight\Assert::assertTypeOf('array', []);
     *  \Cundd\TestFlight\Assert::assertTypeOf('Array', []);
     *  \Cundd\TestFlight\Assert::assertTypeOf('object', new stdClass());
     *  \Cundd\TestFlight\Assert::assertTypeOf('int', 0);
     *  \Cundd\TestFlight\Assert::assertTypeOf('integer', 1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('long', 1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('float', 0.1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('double', 0.1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('numeric', 1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('numeric', 0.1);
     *  \Cundd\TestFlight\Assert::assertTypeOf('numeric', '1');
     *  \Cundd\TestFlight\Assert::assertTypeOf('numeric', '0.1');
     *  \Cundd\TestFlight\Assert::assertTypeOf('null', null);
     *  \Cundd\TestFlight\Assert::assertTypeOf('string', 'a string');
     *  \Cundd\TestFlight\Assert::assertTypeOf('string', 'null');
     *  \Cundd\TestFlight\Assert::assertTypeOf('resource', fopen('php://temp', 'a+'));
     *
     * @param string $type
     * @param mixed  $actual
     * @param string $message
     * @throws AssertionError
     */
    public static function assertTypeOf($type, $actual, $message = '')
    {
        self::$count += 1;

        switch (strtolower($type)) {
            case 'array':
                if (is_array($actual)) {
                    return;
                }
                break;

            case 'resource':
                if (is_resource($actual)) {
                    return;
                }
                break;

            case 'object':
                if (is_object($actual)) {
                    return;
                }
                break;

            case 'int':
            case 'integer':
            case 'long':
                if (is_int($actual)) {
                    return;
                }
                break;

            case 'double':
            case 'float':
                if (is_float($actual)) {
                    return;
                }
                break;

            case 'numeric':
                if (is_numeric($actual)) {
                    return;
                }
                break;

            case 'bool':
            case 'boolean':
                if (is_bool($actual)) {
                    return;
                }
                break;

            case 'callable':
                if (is_callable($actual)) {
                    return;
                }
                break;

            case 'null':
                if (is_null($actual)) {
                    return;
                }
                break;

            case 'string':
                if (is_string($actual)) {
                    return;
                }
                break;

            default:
                throw new AssertionLogicError(sprintf('No assertion defined for type %s', $type));
        }

        if (!$message) {
            $message = sprintf(
                'Failed asserting that %s is of type %s',
                static::getType($actual),
                $type
            );
        }
        throw new AssertionError($message);
    }

    /**
     * Returns the number of preformed assertions
     *
     * @return int
     */
    public static function getCount()
    {
        return self::$count;
    }

    /**
     * @param mixed $input
     * @return string
     */
    private static function getType($input)
    {
        return is_object($input) ? get_class($input) : gettype($input);
    }
}
