<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

use Cundd\TestFlight\FileAnalysis\FileInterface;
use Exception;
use Throwable;

final class EvalError extends Exception
{
    public function __construct(
        FileInterface $file,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->file = $file->getPath();
    }
}
