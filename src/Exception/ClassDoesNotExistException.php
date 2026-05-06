<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

final class ClassDoesNotExistException extends ClassException
{
    /**
     * @param class-string $className
     *
     * @return static
     */
    public static function exceptionWithClassName(
        string $className,
        int $code = 0,
        ?Exception $previous = null,
    ): self {
        return new static(sprintf('Class %s does not exist or can not be found', $className), $code, $previous);
    }
}
