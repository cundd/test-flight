<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

final class NoImplementationForInterfaceException extends ClassException
{
    /**
     * @param interface-string $interfaceName
     */
    public static function exceptionWithInterfaceName(
        string $interfaceName,
        int $code = 0,
        ?Exception $previous = null,
    ): static {
        return new self(
            sprintf('No class found for interface %s', $interfaceName),
            $code,
            $previous
        );
    }
}
