<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

final class FileNotWritableException extends FileException
{
    public static function exceptionForFile(
        string $filePath,
        int $code = 0,
        ?Exception $previous = null,
    ): static {
        return new static(
            sprintf('File %s is not writable', $filePath),
            $code,
            $previous
        );
    }
}
