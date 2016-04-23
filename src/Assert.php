<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 22/04/16
 * Time: 22:08
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\AssertionError;


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
     * @param mixed  $assertion
     * @param string $message
     */
    public static function assert($assertion, string $message = '')
    {
        self::$count += 1;
        assert($assertion, new AssertionError($message));
    }

    /**
     * Test if the callback throws an exception
     *
     * @param callable $callback
     * @param string   $expectedException
     * @param string   $message
     * @throws AssertionError
     */
    public static function throws(callable $callback, string $expectedException = '', string $message = '')
    {
        self::$count += 1;

        $exception = null;
        try {
            $callback();
        } catch (\Error $exception) {

        } catch (\Exception $exception) {

        }

        if ($exception === null) {
            throw new AssertionError($message ?? 'No exception thrown');
        } elseif ($expectedException && !is_a($exception, $expectedException)) {
            throw new AssertionError(
                sprintf(
                    'Expected %s got %s',
                    $expectedException,
                    get_class($exception)
                ) ?? $message
            );
        }
    }

    /**
     * Returns the number of preformed assertions
     *
     * @return int
     */
    public static function getCount():int
    {
        return self::$count;
    }
}