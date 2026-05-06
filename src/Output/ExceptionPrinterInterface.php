<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Definition\DefinitionInterface;
use Throwable;

/**
 * Special printer interface for exception output
 */
interface ExceptionPrinterInterface extends PrinterInterface
{
    /**
     * Print the exception for the given test definition
     */
    public function printException(
        DefinitionInterface $definition,
        Throwable $exception,
    ): self;
}
