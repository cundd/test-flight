<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:03
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\InvalidArgumentException;


/**
 * Class that manages the creation of instances of test classes
 *
 * @package Cundd\TestFlight
 */
class ObjectManager
{
    /**
     * @param string $className
     * @return object
     */
    public function createInstanceOfClass($className)
    {
        $this->assertStringType($className, 'className');

        return new $className();
    }

    /**
     * @param mixed  $input
     * @param string $argumentName
     */
    private function assertStringType($input, $argumentName)
    {
        if (!is_string($input)) {
            throw InvalidArgumentException::exceptionForVariableAndExpectedTypes(
                $argumentName,
                ['string'],
                $input
            );
        }
    }
}