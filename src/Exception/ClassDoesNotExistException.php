<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

class ClassDoesNotExistException extends ClassException
{
    /**
     * @param string $className
     * @param int    $code
     *
     * @return static
     */
    public static function exceptionWithClassName($className, $code = 0, ?Exception $previous = null)
    {
        return new static(sprintf('Class %s does not exist or can not be found', $className), $code, $previous);
    }
}
