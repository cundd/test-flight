<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:37
 */

namespace Cundd\TestFlight\Exception;


class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * @param string          $argumentName
     * @param string[]        $expectedTypes
     * @param mixed           $actualValue
     * @param int             $code
     * @param \Exception|null $previous
     * @return static
     */
    public static function exceptionForVariableAndExpectedTypes(
        $argumentName,
        array $expectedTypes,
        $actualValue,
        $code = 0,
        \Exception $previous = null
    ) {
        $typeInformation = gettype($actualValue);
        if (is_object($actualValue)) {
            $typeInformation .= ' '.get_class($actualValue);
        }

        return new static(
            sprintf(
                'Expected argument %s to be of type(s) %s, %s given',
                $argumentName,
                implode(', ', $expectedTypes),
                $typeInformation
            ),
            $code,
            $previous
        );
    }
}