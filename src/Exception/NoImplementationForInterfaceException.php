<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

class NoImplementationForInterfaceException extends ClassException
{
    /**
     * @param string $interfaceName
     * @param int    $code
     *
     * @return static
     */
    public static function exceptionWithInterfaceName($interfaceName, $code = 0, ?Exception $previous = null)
    {
        return new static(sprintf('No class found for interface %s', $interfaceName), $code, $previous);
    }
}
