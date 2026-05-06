<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Exception;

class FileNotReadableException extends FileException
{
    protected const MESSAGE_TEMPLATE = 'File %s is not readable';

    final public function __construct(
        string $message,
        int $code = 0,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function exceptionForFile(
        string $filePath,
        int $code = 0,
        ?Exception $previous = null,
    ): static {
        return new static(
            sprintf(static::MESSAGE_TEMPLATE, $filePath),
            $code,
            $previous
        );
    }
}
