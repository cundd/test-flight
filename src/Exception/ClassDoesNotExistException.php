<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Exception;


class ClassDoesNotExistException extends ClassException
{
    /**
     * @param string          $className
     * @param int             $code
     * @param \Exception|null $previous
     * @return static
     */
    public static function exceptionWithClassName($className, $code = 0, \Exception $previous = null)
    {
        return new static(sprintf('Class %s does not exist or can not be found', $className), $code, $previous);
    }
}
